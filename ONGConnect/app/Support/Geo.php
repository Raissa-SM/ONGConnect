<?php

namespace App\Support;

class Geo
{
    private const RAIO_TERRA_KM = 6371.0;

    public static function distanciaKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return self::RAIO_TERRA_KM * 2 * asin(sqrt($a));
    }

    public static function fatorProximidade(float $lat1, float $lng1, float $lat2, float $lng2, float $raioMaxKm = 50.0): float
    {
        return max(0.0, 1.0 - (self::distanciaKm($lat1, $lng1, $lat2, $lng2) / $raioMaxKm));
    }

    public static function dentroDe(float $lat1, float $lng1, float $lat2, float $lng2, float $raioKm): bool
    {
        return self::distanciaKm($lat1, $lng1, $lat2, $lng2) <= $raioKm;
    }
}
