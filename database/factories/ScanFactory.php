<?php

namespace Database\Factories;

use App\Models\QrCode;
use App\Models\Scan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Scan>
 */
class ScanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'qr_code_id' => QrCode::factory(),
            'scanned_at' => fake()->dateTimeBetween('-30 days'),
            'device' => fake()->randomElement(['mobile', 'mobile', 'mobile', 'tablet', 'desktop']),
            'referrer_host' => null,
            'country' => fake()->randomElement(['EG', 'SA', 'AE', 'US', 'GB', null]),
            'visitor_hash' => hash('sha256', fake()->uuid()),
        ];
    }
}
