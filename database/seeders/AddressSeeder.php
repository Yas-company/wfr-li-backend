<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        foreach ($users as $user) {


            // Generate 3 addresses per user
            $this->createAddressesForUser($user);
        }
    }

    /**
     * Create 3 addresses for a specific user
     */
    private function createAddressesForUser(User $user): void
    {
        // Base coordinates (Kuwait City area as example)
        $baseLatitude = 29.3759;
        $baseLongitude = 47.9774;

        // If user already has a default address, use its coordinates as base
        $defaultAddress = $user->addresses()->where('is_default', true)->first();
        if ($defaultAddress && $defaultAddress->latitude && $defaultAddress->longitude) {
            $baseLatitude = $defaultAddress->latitude;
            $baseLongitude = $defaultAddress->longitude;
        }

        $addresses = [];

        for ($i = 0; $i < 3; $i++) {
            // Generate random coordinates within 50km radius
            $coordinates = $this->generateRandomCoordinatesWithinRadius(
                $baseLatitude,
                $baseLongitude,
                50 // 50km radius
            );

            $addresses[] = [
                'name' => $this->getAddressName($i),
                'street' => $this->getRandomStreet(),
                'city' => $this->getRandomCity(),
                'phone' => $this->generateRandomPhone(),
                'latitude' => $coordinates['lat'],
                'longitude' => $coordinates['lng'],
                'is_default' => $i === 0, // First address is default
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert all addresses
        Address::insert($addresses);
    }

    /**
     * Generate random coordinates within specified radius (in km)
     */
    private function generateRandomCoordinatesWithinRadius(float $centerLat, float $centerLng, float $radiusKm): array
    {
        // Convert radius to degrees (approximately)
        $radiusDegrees = $radiusKm / 111; // 1 degree ≈ 111 km

        // Generate random angle and distance
        $angle = mt_rand(0, 360) * (M_PI / 180); // Convert to radians
        $distance = sqrt(mt_rand(0, 10000) / 10000) * $radiusDegrees; // Square root for uniform distribution

        // Calculate new coordinates
        $newLat = $centerLat + ($distance * cos($angle));
        $newLng = $centerLng + ($distance * sin($angle) / cos($centerLat * M_PI / 180));

        return [
            'lat' => round($newLat, 8),
            'lng' => round($newLng, 8)
        ];
    }

    /**
     * Get address name based on index
     */
    private function getAddressName(int $index): string
    {
        $names = ['Home', 'Work', 'Office'];
        return $names[$index] ?? 'Address ' . ($index + 1);
    }

    /**
     * Generate random street name
     */
    private function getRandomStreet(): string
    {
        $streets = [
            'Ahmad Al-Jaber Street',
            'Salem Al-Mubarak Street',
            'Fahad Al-Salem Street',
            'Abdullah Al-Mubarak Street',
            'Salmiya Block 10',
            'Hawalli Block 1',
            'Jabriya Block 5',
            'Surra Block 3',
            'Mangaf Block 2',
            'Fahaheel Block 7',
            'Kuwait City Block 1',
            'Shuwaikh Industrial Area',
            'Shamiya Block 2',
            'Khaitan Block 1',
            'Farwaniya Block 6'
        ];

        return $streets[array_rand($streets)] . ', Building ' . mt_rand(1, 100);
    }

    /**
     * Generate random city name
     */
    private function getRandomCity(): string
    {
        $cities = [
            'Kuwait City',
            'Hawalli',
            'Salmiya',
            'Jabriya',
            'Surra',
            'Mangaf',
            'Fahaheel',
            'Khaitan',
            'Farwaniya',
            'Shamiya',
            'Shuwaikh',
            'Sabah Al-Salem',
            'Abdullah Al-Mubarak',
            'Jleeb Al-Shuyoukh',
            'Mahboula'
        ];

        return $cities[array_rand($cities)];
    }

    /**
     * Generate random phone number
     */
    private function generateRandomPhone(): string
    {
        // Kuwait mobile numbers typically start with +965 and then 8 digits
        $prefixes = ['9', '6', '5']; // Common Kuwait mobile prefixes
        $prefix = $prefixes[array_rand($prefixes)];

        return '+965' . $prefix . str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT);
    }
}
