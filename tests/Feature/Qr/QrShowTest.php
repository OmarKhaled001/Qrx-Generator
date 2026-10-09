<?php

namespace Tests\Feature\Qr;

use App\Enums\QrStatus;
use App\Livewire\Dashboard\QrShow;
use App\Models\QrCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SubscribesUsers;
use Tests\TestCase;

class QrShowTest extends TestCase
{
    use RefreshDatabase, SubscribesUsers;

    public function test_owner_sees_the_detail_page(): void
    {
        $this->freePlan();
        $qrCode = QrCode::factory()->create(['name' => 'Spring flyer']);

        $this->actingAs($qrCode->user)->get('/qr/'.$qrCode->id)
            ->assertOk()
            ->assertSee('Spring flyer')
            ->assertSee($qrCode->shortUrl())
            ->assertSee('data:image/svg+xml;base64,', false);
    }

    public function test_other_users_get_403_and_missing_codes_404(): void
    {
        $qrCode = QrCode::factory()->create();
        $intruder = User::factory()->create();

        $this->actingAs($intruder)->get('/qr/'.$qrCode->id)->assertForbidden();
        $this->actingAs($intruder)->get('/qr/999999')->assertNotFound();
        auth()->logout();
        $this->get('/qr/'.$qrCode->id)->assertRedirect('/login');
    }

    public function test_owner_can_pause_and_delete(): void
    {
        $qrCode = QrCode::factory()->create();
        $this->actingAs($qrCode->user);

        Livewire::test(QrShow::class, ['qrCode' => $qrCode])->call('toggleStatus');
        $this->assertSame(QrStatus::Paused, $qrCode->fresh()->status);

        Livewire::test(QrShow::class, ['qrCode' => $qrCode->fresh()])->call('delete')->assertRedirect(route('qr.index'));
        $this->assertModelMissing($qrCode);
    }

    public function test_wifi_password_is_not_shown_on_the_detail_page(): void
    {
        $qrCode = QrCode::factory()->create([
            'type' => 'wifi',
            'payload' => ['ssid' => 'Office', 'password' => 'super-secret-123', 'encryption' => 'WPA', 'hidden' => false],
        ]);

        $this->actingAs($qrCode->user)->get('/qr/'.$qrCode->id)->assertSee('Office')->assertDontSee('super-secret-123');
    }
}
