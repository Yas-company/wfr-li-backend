<?php

namespace App\Http\Services;

use App\Models\Supplier;
use App\Models\User;

class SupplierLocationService
{
    /**
     * Calculate the distance between two points using the Haversine formula
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Radius of the earth in km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Find the nearest supplier to a user
     */
    public function findNearestSupplier(User $user): ?Supplier
    {
        if (!$user->latitude || !$user->longitude) {
            return null;
        }

        $suppliers = Supplier::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $nearestSupplier = null;
        $minDistance = null;

        foreach ($suppliers as $supplier) {
            $distance = $this->calculateDistance(
                $user->latitude,
                $user->longitude,
                $supplier->latitude,
                $supplier->longitude
            );
            $supplier->distance = $distance;

            if ($minDistance === null || $distance < $minDistance) {
                $minDistance = $distance;
                $nearestSupplier = $supplier;
            }
        }

        return $nearestSupplier;
    }

    /**
     * Get suppliers within a certain radius (in kilometers)
     */
    public function getSuppliersWithinRadius(User $user, float $radius = 10): \Illuminate\Support\Collection
    {
        if (!$user->latitude || !$user->longitude) {
            return collect();
        }

        return Supplier::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($supplier) use ($user) {
                $distance = $this->calculateDistance(
                    $user->latitude,
                    $user->longitude,
                    $supplier->latitude,
                    $supplier->longitude
                );
                $supplier->distance = $distance;
                return $supplier;
            })
            ->filter(function ($supplier) use ($radius) {
                return $supplier->distance <= $radius;
            })
            ->sortBy('distance');
    }
}
