<?php

namespace App\Traits;

trait CountDistanceBetweenTwoCoordinates
{
    protected function count($lat1, $long1, $lat2, $long2, $earthRadius = 6371000): float|int
    {
        // convert from degrees to radians
        $latFrom = deg2rad($lat1);
        $longFrom = deg2rad($long1);
        $latTo = deg2rad($lat2);
        $longTo = deg2rad($long2);

        $latDelta = $latTo - $latFrom;
        $longDelta = $longTo - $longFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($longDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    protected function countDistanceByVincentyFormulae($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000): float|int
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
    }
}
