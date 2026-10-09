<?php

namespace Database\Seeders;

use App\Enums\QrStatus;
use App\Enums\QrType;
use App\Models\QrCode;
use App\Models\Scan;
use App\Models\User;
use Database\Factories\QrCodeFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Local-only accounts and sample data (never runs in production).
 * Password for both accounts: "password".
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(['email' => 'admin@qrx.test'], [
            'name' => 'Qrx Admin',
            'password' => Hash::make('password'),
            'locale' => 'en',
        ]);
        $admin->forceFill(['is_admin' => true, 'email_verified_at' => now()])->save();

        $demo = User::query()->updateOrCreate(['email' => 'demo@qrx.test'], [
            'name' => 'Demo User',
            'password' => Hash::make('password'),
            'phone' => '+20 100 000 0000',
            'country' => 'Egypt',
            'city' => 'Cairo',
            'area' => 'Zamalek',
            'locale' => 'en',
        ]);
        $demo->forceFill(['email_verified_at' => now()])->save();

        if ($demo->qrCodes()->exists()) {
            return;
        }

        $names = [
            'url' => 'Restaurant menu', 'text' => 'Welcome sign', 'phone' => 'Call reception',
            'sms' => 'Text for a table', 'email' => 'Feedback email', 'vcard' => 'Business card',
            'wifi' => 'Guest Wi-Fi', 'location' => 'Shop location',
        ];

        foreach (QrType::cases() as $index => $type) {
            $qrCode = QrCode::factory()->for($demo)->create([
                'name' => $names[$type->value],
                'type' => $type,
                'payload' => QrCodeFactory::samplePayload($type),
                'design' => $index % 2
                    ? ['style' => 'round', 'eye' => 'circle', 'color' => '#0f172a', 'bg' => '#ffffff', 'gradient' => ['from' => '#0e7490', 'to' => '#7c3aed'], 'margin' => 1]
                    : ['style' => 'square', 'eye' => 'square', 'color' => '#0e7490', 'bg' => '#ffffff', 'gradient' => null, 'margin' => 1],
                'status' => $type === QrType::Sms ? QrStatus::Paused : QrStatus::Active,
            ]);

            $scans = Scan::factory()->count(5 + $index * 3)->for($qrCode)->create();
            $qrCode->forceFill(['scan_count' => $scans->count(), 'last_scanned_at' => $scans->max('scanned_at')])->save();
        }
    }
}
