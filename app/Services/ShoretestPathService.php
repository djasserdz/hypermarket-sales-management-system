<?php

namespace App\Services;

use Fhaculty\Graph\Graph;
use Graphp\Algorithms\ShortestPath\Dijkstra;
use App\Models\Supermarket;
use App\Models\Supplier;
use App\Models\Location;
use App\Helpers\DistanceHelper;

class ShortestPathService
{
    public static function findNearestSupermarketOrSupplier($product_id, $supermarket_id)
    {
        $graph = new Graph();


        $supermarkets = Supermarket::whereHas('products', fn($query) => $query->where('stock', '>', 0))
                                   ->with('location')
                                   ->get();

        $suppliers = Supplier::whereHas('products', fn($query) => $query->where('stock', '>', 0))->get();


        $origin = Supermarket::with('location')->find($supermarket_id);
        if (!$origin || !$origin->location) {
            return null;
        }

        $originNode = $graph->createVertex($origin->id);
        $nodes = [$origin->id => $originNode];

        foreach ($supermarkets as $supermarket) {
            $nodes[$supermarket->id] = $graph->createVertex($supermarket->id);
        }
        foreach ($suppliers as $supplier) {
            $nodes[$supplier->id] = $graph->createVertex($supplier->id);
        }


        foreach ($supermarkets as $super1) {
            foreach ($supermarkets as $super2) {
                if ($super1->id !== $super2->id) {
                    $distance = DistanceHelper::calculateDistance(
                        $super1->location->latitude, $super1->location->longitude,
                        $super2->location->latitude, $super2->location->longitude
                    );
                    $nodes[$super1->id]->createEdgeTo($nodes[$super2->id])->setWeight($distance);
                }
            }
        }


        foreach ($supermarkets as $supermarket) {
            foreach ($suppliers as $supplier) {
                $distance = DistanceHelper::calculateDistance(
                    $supermarket->location->latitude, $supermarket->location->longitude,
                    $supplier->latitude, $supplier->longitude
                );
                $nodes[$supermarket->id]->createEdgeTo($nodes[$supplier->id])->setWeight($distance);
            }
        }


        $algorithm = new Dijkstra($originNode);
        $shortestPaths = $algorithm->getEdges();


        foreach ($shortestPaths as $edge) {
            $destination = $edge->getVertexEnd();
            $destinationId = $destination->getId();

            if (isset($nodes[$destinationId])) {
                return ['type' => isset($supermarkets->keyBy('id')[$destinationId]) ? 'supermarket' : 'supplier', 'id' => $destinationId];
            }
        }

        return null;
    }
}
