<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

/**
 * The plan catalogue. Paid plans read their Stripe price IDs from
 * config/qrx.php (placeholders in .env.example) so checkout works once real
 * Stripe keys and prices are configured.
 */
class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'slug' => 'free', 'name' => 'Free', 'stripe_price_id' => null, 'price_cents' => 0,
                'qr_limit' => config('qrx.free_plan.qr_limit'), 'scan_limit' => config('qrx.free_plan.scan_limit'),
                'is_free' => true, 'sort_order' => 0,
                'features' => ['3 dynamic QR codes', '100 scans per QR code', 'All 8 QR types', 'PNG and SVG downloads'],
            ],
            [
                'slug' => 'starter', 'name' => 'Starter', 'stripe_price_id' => config('qrx.stripe_prices.starter'),
                'price_cents' => 900, 'qr_limit' => 25, 'scan_limit' => 5000, 'sort_order' => 1,
                'features' => ['25 dynamic QR codes', '5,000 scans per QR code', 'Scan analytics', 'PNG, SVG and EPS downloads'],
            ],
            [
                'slug' => 'business', 'name' => 'Business', 'stripe_price_id' => config('qrx.stripe_prices.business'),
                'price_cents' => 2900, 'qr_limit' => 100, 'scan_limit' => 50000, 'sort_order' => 2,
                'features' => ['100 dynamic QR codes', '50,000 scans per QR code', 'Scan analytics', 'PNG, SVG and EPS downloads'],
            ],
            [
                'slug' => 'agency', 'name' => 'Agency', 'stripe_price_id' => config('qrx.stripe_prices.agency'),
                'price_cents' => 7900, 'qr_limit' => 500, 'scan_limit' => 500000, 'sort_order' => 3,
                'features' => ['500 dynamic QR codes', '500,000 scans per QR code', 'Scan analytics', 'Priority support'],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::query()->updateOrCreate(
                ['slug' => $plan['slug']],
                $plan + ['currency' => 'usd', 'interval' => 'month', 'is_free' => false, 'is_active' => true],
            );
        }
    }
}
