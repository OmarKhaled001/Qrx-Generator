<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // plan_id / price_id are placeholders: replace them with the Stripe product and
        // price IDs (in the Filament admin) before testing checkout against Stripe.
        $plans = [
            ['title' => 'Starter', 'price' => 9, 'qr_count' => 10, 'scan_count' => 1000],
            ['title' => 'Business', 'price' => 29, 'qr_count' => 50, 'scan_count' => 10000],
            ['title' => 'Agency', 'price' => 79, 'qr_count' => 250, 'scan_count' => 100000],
        ];

        foreach ($plans as $plan) {
            $key = strtolower($plan['title']);

            Plan::updateOrCreate(
                ['plan_id' => 'prod_'.$key],
                $plan + ['price_id' => 'price_'.$key, 'is_active' => true],
            );
        }

        if (app()->isProduction()) {
            return;
        }

        // Local development accounts (password: "password").
        Admin::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')],
        );

        User::updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'phone' => '+20 100 000 0000',
                'country' => 'Egypt',
                'city' => 'Cairo',
                'area' => 'Nasr City',
                'password' => Hash::make('password'),
            ],
        );
    }
}
