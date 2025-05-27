<?php
namespace App\Services;

use App\Helpers\DistanceHelper;
// use App\Models\Transfer; // Removing singular import as plural is instantiated
use App\Models\supermarket as ModelsSupermarket;
use App\Models\Transfers; // Keeping plural import to match instantiation
use App\Models\SupplierOrder;
use App\Models\Product;
use Fhaculty\Graph\Graph;
use Graphp\Algorithms\ShortestPath\Dijkstra;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Added Carbon import
// use Illuminate\Support\Facades\Log; // Optional: for logging errors

class ShortestPathService
{
    private const LOW_STOCK_THRESHOLD = 20; // Define low stock threshold

    /**
     * Helper function to create a supplier order.
     */
    private function createSupplierOrder(int $productId, int $requestingSupermarketId, int $quantityNeeded, string $notes = ''): array
    {
        $product = Product::with('supplier')->find($productId); // Eager load supplier
        if (!$product) {
            return ['message' => 'Product not found. Cannot create supplier order.'];
        }
        if (!$product->supplier_id || !$product->supplier) {
            return ['message' => 'Product supplier information missing. Cannot create supplier order.'];
        }

        try {
            DB::beginTransaction();
            $order = SupplierOrder::create([
                'product_id' => $productId,
                'supplier_id' => $product->supplier_id,
                'supermarket_id' => $requestingSupermarketId,
                'quantity_ordered' => $quantityNeeded,
                'status' => 'pending_approval', // Default status
                'notes' => $notes ?: 'Order automatically created due to stockout or no suitable transfer source.',
            ]);
            DB::commit();
            return [
                'message' => 'Supplier order created successfully.',
                'supplier_order_id' => $order->id,
                'product_name' => $product->name,
                'supplier_name' => $product->supplier->name,
                'quantity_ordered' => $quantityNeeded,
                'status' => $order->status,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error("Supplier order creation failed: " . $e->getMessage()); // Optional logging
            return ['message' => 'Failed to create supplier order: ' . $e->getMessage()];
        }
    }

    /**
     * Calculate shortest path and create transfer request or supplier order
     * 
     * @param int $fromSupermarketId The supermarket ID where product is out of stock
     * @param int $productId The product ID needed
     * @param int $quantityNeeded The quantity of product needed
     * @return array Result with path, distance, and transfer/order information
     */
    public function calculateAndCreateTransfer(int $fromSupermarketId, int $productId, int $quantityNeeded = 100): array
    {
        // Validate quantity
        if ($quantityNeeded <= 0) {
            throw new \InvalidArgumentException("Quantity needed must be greater than zero.");
        }

        // Load all supermarkets with their locations and products with pivot data
        $supermarkets = ModelsSupermarket::with(['location', 'products' => function($query) {
            $query->withPivot('quantity');
        }])->get();
        
        $fromSupermarket = $supermarkets->firstWhere('id', $fromSupermarketId);
        
        if (!$fromSupermarket || !$fromSupermarket->location) {
            // If the requesting supermarket itself is not found or has no location, we can't proceed.
            // For now, let's assume this is an invalid request state.
             throw new \Exception("Starting supermarket or its location not found.");
        }
        
        // Find supermarkets with enough stock of the product and that won't go below threshold
        $targetSupermarkets = $supermarkets->filter(function ($market) use ($productId, $quantityNeeded, $fromSupermarketId) {
            if (!$market->location) return false;
            if ($market->id === $fromSupermarketId) return false; // Cannot transfer from/to itself

            $productWithStock = $market->products->firstWhere('id', $productId);
            
            return $productWithStock &&
                   $productWithStock->pivot->quantity >= $quantityNeeded &&
                   ($productWithStock->pivot->quantity - $quantityNeeded) >= self::LOW_STOCK_THRESHOLD;
        });
        
        if ($targetSupermarkets->isEmpty()) {
            // No supermarket has sufficient quantity OR all would become low stock.
            return $this->createSupplierOrder(
                $productId,
                $fromSupermarketId,
                $quantityNeeded,
                'No suitable supermarket for transfer found (insufficient stock, would result in low stock at source, or self-transfer attempted).'
            );
        }
        
        // Build graph with distance-based edges
        $graph = new Graph();
        $vertexMap = [];
        
        // Create vertices for supermarkets with locations (including the source and potential targets)
        $allRelevantSupermarkets = $supermarkets->filter(function ($market) use ($fromSupermarketId, $targetSupermarkets) {
            return $market->location && ($market->id === $fromSupermarketId || $targetSupermarkets->contains('id', $market->id));
        });

        foreach ($allRelevantSupermarkets as $market) {
            $vertexMap[$market->id] = $graph->createVertex((string)$market->id);
        }
        
        // Ensure fromSupermarketId vertex exists if not already added (should be covered by above)
        if (!isset($vertexMap[$fromSupermarketId]) && $fromSupermarket->location) {
             // This might indicate $fromSupermarket was not in $allRelevantSupermarkets, which is unlikely if it has a location.
            $vertexMap[$fromSupermarketId] = $graph->createVertex((string)$fromSupermarketId);
        }

        // Create edges between relevant supermarkets
        $maxDistanceThreshold = 80000; // Consider making this configurable
        $relevantIds = $allRelevantSupermarkets->pluck('id')->values();
        
        for ($i = 0; $i < $relevantIds->count(); $i++) {
            for ($j = $i + 1; $j < $relevantIds->count(); $j++) {
                $idA = $relevantIds[$i];
                $idB = $relevantIds[$j];

                // Supermarket models $a and $b are already fetched in $allRelevantSupermarkets
                $a = $allRelevantSupermarkets->firstWhere('id', $idA);
                $b = $allRelevantSupermarkets->firstWhere('id', $idB);
                
                // Since $allRelevantSupermarkets are pre-filtered for location, $a, $b, $a->location, $b->location should exist
                // Also, their vertices should be in $vertexMap
                if (!$a || !$b || !isset($vertexMap[$a->id]) || !isset($vertexMap[$b->id])) continue; 
                
                $distance = DistanceHelper::calculate(
                    $a->location->latitude,
                    $a->location->longitude,
                    $b->location->latitude,
                    $b->location->longitude
                );
                
                if ($distance <= $maxDistanceThreshold) {
                    $vertexMap[$a->id]->createEdgeTo($vertexMap[$b->id])->setWeight($distance);
                    $vertexMap[$b->id]->createEdgeTo($vertexMap[$a->id])->setWeight($distance);
                }
            }
        }
        
        if (!isset($vertexMap[$fromSupermarketId])) {
            return $this->createSupplierOrder(
                $productId,
                $fromSupermarketId,
                $quantityNeeded,
                'Starting supermarket not found in graph for path calculation.'
            );
        }
        $startVertex = $vertexMap[$fromSupermarketId];
        $shortestDistance = INF;
        $destinationSupermarket = null;
        $actualPath = [];
        
        foreach ($targetSupermarkets as $target) {
            if (!isset($vertexMap[$target->id])) continue; 
            
            $targetVertex = $vertexMap[$target->id];
            // Check if start and target vertices are the same, which shouldn't happen due to earlier filter
            if ($startVertex === $targetVertex) continue;

            $algorithm = new Dijkstra($startVertex);

            try {
                if (!$algorithm->hasVertex($targetVertex)) continue; // Target vertex not in graph processed by Dijkstra

                $currentDistance = $algorithm->getDistance($targetVertex);
                
                if ($currentDistance < $shortestDistance) {
                    $shortestDistance = $currentDistance;
                    $destinationSupermarket = $target;
                    $walk = $algorithm->getWalkTo($targetVertex);
                    $actualPath = $walk->getVertices()->getIds();
                }
            } catch (\OutOfBoundsException $e) {
                continue; 
            } catch (\Exception $e) {
                // Log::error("Dijkstra algorithm error: " . $e->getMessage()); // Optional logging
                continue;
            }
        }
        
        if (!$destinationSupermarket) {
            return $this->createSupplierOrder(
                $productId,
                $fromSupermarketId,
                $quantityNeeded,
                'No reachable supermarket found that meets stock criteria.'
            );
        }
        
        $existingTransferToday = Transfers::where('product_id', $productId)
                                          ->where('to_supermarket', $fromSupermarketId)
                                          ->whereDate('created_at', Carbon::today())
                                          ->exists();

        if ($existingTransferToday) {
            return [
                'path' => $actualPath,
                'distance_km' => is_finite($shortestDistance) ? round($shortestDistance / 1000, 2) : null,
                'destination_supermarket' => $destinationSupermarket->name ?? null,
                'message' => 'A transfer request for this product to your supermarket has already been initiated today.'
            ];
        }
        
        try {
            DB::beginTransaction();
            
            $transfer = new Transfers();
            $transfer->product_id = $productId;
            $transfer->from_supermarket = $destinationSupermarket->id;
            $transfer->to_supermarket = $fromSupermarketId;
            $transfer->quantity = $quantityNeeded;
            $transfer->status = 'pending';
            $transfer->save();
            
            DB::commit();
            
            return [
                'path' => $actualPath,
                'distance_km' => is_finite($shortestDistance) ? round($shortestDistance / 1000, 2) : null,
                'destination_supermarket' => $destinationSupermarket->name ?? null,
                'transfer_id' => $transfer->id,
                'message' => 'Transfer request created successfully.'
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error("Transfer creation failed: " . $e->getMessage()); // Optional logging
            return [
                'path' => $actualPath,
                'distance_km' => is_finite($shortestDistance) ? round($shortestDistance / 1000, 2) : null,
                'destination_supermarket' => $destinationSupermarket->name ?? null,
                'message' => 'Found shortest path but failed to create transfer: ' . $e->getMessage()
            ];
        }
    }
}