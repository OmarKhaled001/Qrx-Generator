<?php

namespace Tests\Feature\Domain;

use App\Models\QrCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class QrCodePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owners_can_act_on_their_own_qr_codes(): void
    {
        $qrCode = QrCode::factory()->create();
        $gate = Gate::forUser($qrCode->user);

        foreach (['view', 'update', 'delete'] as $ability) {
            $this->assertTrue($gate->allows($ability, $qrCode), $ability);
        }
    }

    public function test_other_customers_cannot(): void
    {
        $qrCode = QrCode::factory()->create();
        $gate = Gate::forUser(User::factory()->create());

        foreach (['view', 'update', 'delete'] as $ability) {
            $this->assertFalse($gate->allows($ability, $qrCode), $ability);
        }
    }

    public function test_admins_can_act_on_every_qr_code(): void
    {
        $qrCode = QrCode::factory()->create();
        $gate = Gate::forUser(User::factory()->admin()->create());

        foreach (['viewAny', 'view', 'update', 'delete', 'deleteAny'] as $ability) {
            $this->assertTrue($gate->allows($ability, in_array($ability, ['viewAny', 'deleteAny'], true) ? QrCode::class : $qrCode), $ability);
        }
    }

    public function test_is_admin_cannot_be_mass_assigned(): void
    {
        $user = User::factory()->create();
        $user->fill(['is_admin' => true])->save();

        $this->assertFalse($user->fresh()->is_admin);
    }
}
