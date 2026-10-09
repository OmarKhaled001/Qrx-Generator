<?php

namespace Tests\Concerns;

use App\Models\FeatureEmail;
use App\Models\FeatureMessage;
use App\Models\FeaturePhone;
use App\Models\FeatureText;
use App\Models\FeatureUrl;
use App\Models\FeatureVcard;
use App\Models\FeatureWifi;
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

        $defaults = [
            'text' => ['text' => 'hello'],
            'url' => ['url' => 'https://example.com'],
            'phone' => ['phone' => '+201000000000'],
            'message' => ['phone' => '+201000000000', 'message' => 'Hi there'],
            'email' => ['email' => 'hello@example.com', 'subject' => 'Hello', 'body' => 'Body text'],
            'vcard' => ['full_name' => 'Jane Doe', 'email' => 'jane@example.com', 'phone' => '+201000000001', 'tel' => '+20220000000', 'address' => 'Cairo', 'note' => 'Note'],
            'wifi' => ['wireless_ssid' => 'Office', 'password' => 'secret-pass', 'encryption' => 'WPA/WEP'],
        ];

        $model = [
            'text' => FeatureText::class,
            'url' => FeatureUrl::class,
            'phone' => FeaturePhone::class,
            'message' => FeatureMessage::class,
            'email' => FeatureEmail::class,
            'vcard' => FeatureVcard::class,
            'wifi' => FeatureWifi::class,
        ][$type];

        $model::forceCreate($feature + $defaults[$type] + ['qrx_code_id' => $qrxCode->id]);

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
