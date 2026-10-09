<?php

namespace Tests\Feature;

use App\Http\Livewire\QrxEdit;
use App\Models\Admin;
use App\Models\QrxCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Tests\Concerns\CreatesQrxCodes;
use Tests\TestCase;

class QrxCodeAuthorizationTest extends TestCase
{
    use CreatesQrxCodes, RefreshDatabase;

    private User $owner;
    private User $intruder;
    private QrxCode $qrxCode;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->owner = User::factory()->create();
        $this->intruder = User::factory()->create();
        $this->qrxCode = $this->createQrxCode($this->owner);
    }

    public function test_user_cannot_open_the_edit_page_of_another_users_qr(): void
    {
        $this->actingAs($this->intruder)
            ->get(route('dashboard.qr.edit', $this->qrxCode->id))
            ->assertForbidden();
    }

    public function test_user_cannot_change_the_status_of_another_users_qr(): void
    {
        $this->actingAs($this->intruder)
            ->put(route('dashboard.qr.status', $this->qrxCode->id))
            ->assertForbidden();

        $this->assertSame('active', $this->qrxCode->fresh()->status);
    }

    public function test_user_cannot_delete_another_users_qr(): void
    {
        Storage::disk('public')->put($this->qrxCode->path, 'png');

        $this->actingAs($this->intruder)
            ->delete(route('dashboard.qr.delete', $this->qrxCode->id))
            ->assertForbidden();

        $this->assertModelExists($this->qrxCode);
        Storage::disk('public')->assertExists($this->qrxCode->path);
    }

    public function test_user_cannot_download_another_users_qr(): void
    {
        $this->actingAs($this->intruder)
            ->get(route('dashboard.qr.download', ['id' => $this->qrxCode->id, 'type' => 'svg', 'size' => 300]))
            ->assertForbidden();
    }

    public function test_user_cannot_save_another_users_qr_through_the_edit_component(): void
    {
        $this->subscribe($this->intruder);
        $this->actingAs($this->intruder);

        Livewire::test(QrxEdit::class, ['qrx' => $this->qrxCode])
            ->set('qrxName', 'Stolen')
            ->call('generat')
            ->assertStatus(403);

        $fresh = $this->qrxCode->fresh();
        $this->assertSame($this->owner->id, (int) $fresh->user_id);
        $this->assertSame('My QR', $fresh->name);
    }

    public function test_owner_can_open_the_edit_page(): void
    {
        $this->actingAs($this->owner)
            ->get(route('dashboard.qr.edit', $this->qrxCode->id))
            ->assertOk();
    }

    public function test_owner_can_change_the_status_of_their_qr(): void
    {
        $this->actingAs($this->owner)
            ->put(route('dashboard.qr.status', $this->qrxCode->id))
            ->assertRedirect();

        $this->assertSame('pause', $this->qrxCode->fresh()->status);
    }

    public function test_owner_can_delete_their_qr(): void
    {
        Storage::disk('public')->put($this->qrxCode->path, 'png');

        $this->actingAs($this->owner)
            ->delete(route('dashboard.qr.delete', $this->qrxCode->id))
            ->assertRedirect();

        $this->assertModelMissing($this->qrxCode);
        Storage::disk('public')->assertMissing($this->qrxCode->path);
    }

    public function test_owner_can_download_their_qr(): void
    {
        $this->actingAs($this->owner)
            ->get(route('dashboard.qr.download', ['id' => $this->qrxCode->id, 'type' => 'svg', 'size' => 300, 'name' => 'My QR']))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml')
            ->assertDownload('my-qr.svg');
    }

    // Saving regenerates the stored PNG, which simple-qrcode renders through imagick.
    #[RequiresPhpExtension('imagick')]
    public function test_owner_can_save_their_qr_through_the_edit_component(): void
    {
        $this->subscribe($this->owner);
        $this->actingAs($this->owner);

        Livewire::test(QrxEdit::class, ['qrx' => $this->qrxCode])
            ->set('qrxName', 'Renamed')
            ->set('text', 'updated text')
            ->call('generat')
            ->assertRedirect(route('dashboard.qr.all'));

        $this->assertSame('Renamed', $this->qrxCode->fresh()->name);
        $this->assertSame($this->owner->id, (int) $this->qrxCode->fresh()->user_id);
    }

    public function test_missing_qr_returns_not_found_for_write_actions(): void
    {
        $this->actingAs($this->owner)
            ->put(route('dashboard.qr.status', 999999))
            ->assertNotFound();

        $this->actingAs($this->owner)
            ->delete(route('dashboard.qr.delete', 999999))
            ->assertNotFound();
    }

    public function test_filament_admins_keep_access_to_every_qr(): void
    {
        $admin = Admin::forceCreate([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $gate = Gate::forUser($admin);

        $this->assertTrue($gate->allows('view', $this->qrxCode));
        $this->assertTrue($gate->allows('update', $this->qrxCode));
        $this->assertTrue($gate->allows('delete', $this->qrxCode));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->put(route('dashboard.qr.status', $this->qrxCode->id))
            ->assertRedirect(route('login'));
    }
}
