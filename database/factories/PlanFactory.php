<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'stripe_price_id' => 'price_'.Str::lower(Str::random(14)),
            'price_cents' => 900,
            'currency' => 'usd',
            'interval' => 'month',
            'qr_limit' => 25,
            'scan_limit' => 5000,
            'is_free' => false,
            'is_active' => true,
            'sort_order' => 1,
            'features' => null,
        ];
    }

    public function free(): static
    {
        return $this->state(fn () => [
            'name' => 'Free',
            'slug' => 'free',
            'stripe_price_id' => null,
            'price_cents' => 0,
            'qr_limit' => 3,
            'scan_limit' => 100,
            'is_free' => true,
            'sort_order' => 0,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
