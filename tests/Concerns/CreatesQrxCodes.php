<?php

namespace Tests\Concerns;

use App\Models\FeatureText;
use App\Models\FeatureUrl;
use App\Models\Plan;
use App\Models\QrxCode;
use App\Models\Style;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait CreatesQrxCodes
{
    protected function createQrxCode(User $owner, string $type = 'text', array $feature = []): QrxCode
    {
        $code = 'qx-'.Str::lower(Str::random(10));

        $qrxCode = QrxCode::forceCreate([
            'name' => 'My QR',
            'type' => $type,
            'code' => $code,
            'path' => 'qrxs/'.$code.'.png',
            'status' => 'active',
            'user_id' => $owner->id,
        ]);

        Style::forceCreate([
            'size' => 200,
            'style' => 'square',
            'color' => '#000000',
            'bg_color' => '#ffffff',
            'eye' => 'square',
            'margin' => 1,
            'qrx_code_id' => $qrxCode->id,
        ]);

        match ($type) {
            'url' => FeatureUrl::forceCreate(['url' => $feature['url'] ?? 'https://example.com', 'qrx_code_id' => $qrxCode->id]),
            default => FeatureText::forceCreate(['text' => $feature['text'] ?? 'hello', 'qrx_code_id' => $qrxCode->id]),
        };

        return $qrxCode;
    }

    /**
     * Give the user an active Stripe subscription to a plan, as Cashier would store it.
     */
    protected function subscribe(User $user, int $qrCount = 10, int $scanCount = 100): Plan
    {
        $plan = Plan::forceCreate([
            'plan_id' => 'prod_test',
            'price_id' => 'price_test',
            'qr_count' => $qrCount,
            'scan_count' => $scanCount,
            'title' => 'Test plan',
            'price' => 10,
            'is_active' => true,
        ]);

        $subscriptionId = DB::table('subscriptions')->insertGetId([
            'user_id' => $user->id,
            'type' => 'default',
            'stripe_id' => 'sub_'.Str::random(10),
            'stripe_status' => 'active',
            'stripe_price' => 'price_test',
            'quantity' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('subscription_items')->insert([
            'subscription_id' => $subscriptionId,
            'stripe_id' => 'si_'.Str::random(10),
            'stripe_product' => 'prod_test',
            'stripe_price' => 'price_test',
            'quantity' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $plan;
    }
}
