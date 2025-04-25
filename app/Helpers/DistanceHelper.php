<?php

namespace App\Helpers;

class DistanceHelper
{
    public static function calculate(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;
        $c = 2 * asin(sqrt($a));

        return $earthRadius * $c;
    }
}