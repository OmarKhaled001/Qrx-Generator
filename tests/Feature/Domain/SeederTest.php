<?php

namespace Tests\Feature\Domain;

use App\Enums\QrType;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_creates_plans_accounts_and_a_qr_of_every_type(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class); // idempotent

        $this->assertSame(['free', 'starter', 'business', 'agency'], Plan::query()->orderBy('sort_order')->pluck('slug')->all());
        $this->assertSame(1, Plan::query()->where('is_free', true)->count());

        $admin = User::query()->where('email', 'admin@qrx.test')->sole();
        $this->assertTrue($admin->is_admin);
        $this->assertTrue($admin->hasVerifiedEmail());

        $demo = User::query()->where('email', 'demo@qrx.test')->sole();
        $this->assertFalse($demo->is_admin);
        $this->assertEqualsCanonicalizing(
            array_map(fn (QrType $type) => $type->value, QrType::cases()),
            $demo->qrCodes->map(fn ($qrCode) => $qrCode->type->value)->all(),
        );
        $this->assertGreaterThan(0, $demo->qrCodes()->sum('scan_count'));
    }
}
