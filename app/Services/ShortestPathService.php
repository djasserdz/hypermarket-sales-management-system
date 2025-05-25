<?php
namespace App\Services;

use App\Helpers\DistanceHelper;
// use App\Models\Transfer; // Removing singular import as plural is instantiated
use App\Models\supermarket as ModelsSupermarket;
use App\Models\Transfers; // Keeping plural import to match instantiation
use Fhaculty\Graph\Graph;
use Graphp\Algorithms\ShortestPath\Dijkstra;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Added Carbon import

class ShortestPathService
{
    /**
     * Calculate shortest path and create transfer request
     * 
     * @param int $fromSupermarketId The supermarket ID where product is out of stock
     * @param int $productId The product ID needed
     * @param int $quantityNeeded The quantity of product needed
     * @return array Result with path, distance, and transfer information
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
            throw new \Exception("Starting supermarket location not found.");
        }
        
        // Find supermarkets with enough stock of the product
        $targetSupermarkets = $supermarkets->filter(function ($market) use ($productId, $quantityNeeded) {
            if (!$market->location) return false;
            
            $productWithStock = $market->products->firstWhere('id', $productId);
            return $productWithStock && $productWithStock->pivot->quantity >= $quantityNeeded;
        });
        
        if ($targetSupermarkets->isEmpty()) {
            return [
                'path' => null,
                'distance_km' => null,
                'destination_supermarket' => null,
                'message' => 'No supermarket has sufficient quantity of the product. Contact supplier.'
            ];
        }
        
        // Build graph with distance-based edges
        $graph = new Graph();
        $vertexMap = [];
        
        // Create vertices for supermarkets with locations
        foreach ($supermarkets as $market) {
            if (!$market->location) continue;
            $vertexMap[$market->id] = $graph->createVertex((string)$market->id);
        }
        
        // Create edges between supermarkets with a maximum distance threshold
        $maxDistanceThreshold = 80000;
        $ids = $supermarkets->pluck('id')->values();
        
        for ($i = 0; $i < count($ids); $i++) {
            for ($j = $i + 1; $j < count($ids); $j++) {
                $a = $supermarkets->firstWhere('id', $ids[$i]);
                $b = $supermarkets->firstWhere('id', $ids[$j]);
                
                if (!$a->location || !$b->location) continue;
                
                $distance = DistanceHelper::calculate(
                    $a->location->latitude,
                    $a->location->longitude,
                    $b->location->latitude,
                    $b->location->longitude
                );
                
                // Only create edges between reasonably close supermarkets
                if ($distance <= $maxDistanceThreshold) {
                    $vertexMap[$a->id]->createEdgeTo($vertexMap[$b->id])->setWeight($distance);
                    $vertexMap[$b->id]->createEdgeTo($vertexMap[$a->id])->setWeight($distance);
                }
            }
        }
        
        // Find the nearest supermarket with sufficient stock
        $startVertex = $vertexMap[$fromSupermarketId];
        $shortestDistance = INF;
        $destinationSupermarket = null;
        $actualPath = [];
        
        foreach ($targetSupermarkets as $target) {
            if (!isset($vertexMap[$target->id])) continue;
            
            $targetVertex = $vertexMap[$target->id];
            $algorithm = new Dijkstra($startVertex);

            try {
                // Get the distance
                $currentDistance = $algorithm->getDistance($targetVertex);
                
                if ($currentDistance < $shortestDistance) {
                    $shortestDistance = $currentDistance;
                    $destinationSupermarket = $target;
                    
                    // Attempt to get the actual path
                    $walk = $algorithm->getWalkTo($targetVertex);
                    $pathVertices = $walk->getVertices()->getIds();
                    $actualPath = $pathVertices;
                }
            } catch (\OutOfBoundsException $e) {
                // Target vertex is not reachable
                continue;
            } catch (\Exception $e) {
                // Other general exceptions from graph algorithm
                continue;
            }
        }
        
        // This check is crucial
        if (!$destinationSupermarket) {
            return [
                'path' => null,
                'distance_km' => null,
                'destination_supermarket' => null,
                'message' => 'No path to any supermarket with sufficient product quantity.'
            ];
        }
        
        // Check if a transfer for this product to this supermarket was already created today
        $existingTransferToday = Transfers::where('product_id', $productId)
                                          ->where('to_supermarket', $fromSupermarketId) // Supermarket needing stock
                                          ->whereDate('created_at', Carbon::today())
                                          ->exists();

        if ($existingTransferToday) {
            return [
                              'message' => 'A transfer request for this product to your supermarket has already been initiated today.'
            ];
        }
        
        try {
            DB::beginTransaction();
            
            $transfer = new Transfers(); // Instantiating plural, matches plural import
            $transfer->product_id = $productId;
            $transfer->from_supermarket = $destinationSupermarket->id;
            $transfer->to_supermarket = $fromSupermarketId;
            $transfer->quantity = $quantityNeeded;
            $transfer->status = 'pending';
            $transfer->save();
            
            DB::commit();
            
            return [
                'path' => $actualPath,
                'distance_km' => is_finite($shortestDistance) ? round($shortestDistance, 2) : null,
                'destination_supermarket' => $destinationSupermarket->name ?? null,
                'transfer_id' => $transfer->id,
                'message' => 'Transfer request created successfully.'
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'path' => $actualPath,
                'distance_km' => is_finite($shortestDistance) ? round($shortestDistance, 2) : null,
                'destination_supermarket' => $destinationSupermarket->name ?? null,
                'message' => 'Found shortest path but failed to create transfer: ' . $e->getMessage()
            ];
        }
    }
}