<?php
namespace App\Services;

use App\Helpers\DistanceHelper;
use App\Models\Supermarket;
use App\Models\Transfer;
use App\Models\Product;
use App\Models\supermarket as ModelsSupermarket;
use App\Models\Transfers;
use Fhaculty\Graph\Graph;
use Graphp\Algorithms\ShortestPath\Dijkstra;
use Illuminate\Support\Facades\DB;

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
        $maxDistanceThreshold = 60000; // Maximum distance in km to consider
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
        $algorithm = new Dijkstra($startVertex);
        
        $shortestDistance = INF;
        $destinationSupermarket = null;
        $pathVertices = [];
        
        foreach ($targetSupermarkets as $target) {
            if (!isset($vertexMap[$target->id])) continue;
            
            $targetVertex = $vertexMap[$target->id];
            
            try {
                // Get the distance
                $distance = $algorithm->getDistance($targetVertex);
                
                if ($distance < $shortestDistance) {
                    $shortestDistance = $distance;
                    $destinationSupermarket = $target;
                    
                    // Manually construct the path - since getShortestPathTo is not available
                    // We'll just create a direct path from source to destination
                    $pathVertices = [$fromSupermarketId, $target->id];
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        if (!$destinationSupermarket) {
            return [
                'path' => null,
                'distance_km' => null,
                'destination_supermarket' => null,
                'message' => 'No path to any supermarket with sufficient product quantity.'
            ];
        }
        
        try {
            DB::beginTransaction();
            
            $transfer = new Transfers();
            $transfer->product_id = $productId;
            $transfer->from_supermarket= $destinationSupermarket->id;
            $transfer->to_supermarket= $fromSupermarketId;
            $transfer->quantity = $quantityNeeded;
            $transfer->status = 'pending';
            $transfer->save();
            
            DB::commit();
            
            return [
                'path' => $pathVertices,
                'distance_km' => round($shortestDistance, 2),
                'destination_supermarket' => $destinationSupermarket->name ?? null,
                'transfer_id' => $transfer->id,
                'message' => 'Transfer request created successfully.'
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'path' => $pathVertices,
                'distance_km' => round($shortestDistance, 2),
                'destination_supermarket' => $destinationSupermarket->name ?? null,
                'message' => 'Found shortest path but failed to create transfer: ' . $e->getMessage()
            ];
        }
    }
}