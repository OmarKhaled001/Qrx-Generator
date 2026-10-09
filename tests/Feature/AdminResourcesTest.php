<?php

namespace Tests\Feature;

use App\Filament\Resources\QrxCodeResource\Pages\EditQrxCode;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\Concerns\CreatesQrxCodes;
use Tests\TestCase;

/**
 * Characterization of the MVP admin resources (users, QR codes) before the v2 rewrite.
 */
class AdminResourcesTest extends TestCase
{
    use CreatesQrxCodes, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]), 'web');
    }

    public function test_admin_can_list_and_open_users(): void
    {
        $user = User::factory()->create(['name' => 'Customer One']);

        $this->get('/admin/users')->assertOk()->assertSee('Customer One');
        $this->get("/admin/users/{$user->id}/edit")->assertOk();
    }

    public function test_admin_can_list_and_open_qr_codes_of_any_user(): void
    {
        $qrxCode = $this->createQrxCode(User::factory()->create());
        $qrxCode->forceFill(['name' => 'Customer QR'])->save();

        $this->get('/admin/qrx-codes')->assertOk()->assertSee('Customer QR');
        $this->get("/admin/qrx-codes/{$qrxCode->id}/edit")->assertOk();
    }

    public function test_admin_can_pause_any_qr_code(): void
    {
        $qrxCode = $this->createQrxCode(User::factory()->create());

        Livewire::test(EditQrxCode::class, ['record' => $qrxCode->getRouteKey()])
            ->fillForm(['status' => 'pause'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('pause', $qrxCode->fresh()->status);
    }
}
