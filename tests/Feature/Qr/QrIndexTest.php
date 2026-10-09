<?php

namespace Tests\Feature\Qr;

use App\Enums\QrStatus;
use App\Livewire\Dashboard\QrIndex;
use App\Models\QrCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SubscribesUsers;
use Tests\TestCase;

class QrIndexTest extends TestCase
{
    use RefreshDatabase, SubscribesUsers;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->freePlan(); // scan limit 100
        $this->owner = User::factory()->create();
        QrCode::factory()->for($this->owner)->create(['name' => 'Live menu']);
        QrCode::factory()->for($this->owner)->paused()->create(['name' => 'Paused flyer']);
        QrCode::factory()->for($this->owner)->create(['name' => 'Old poster', 'scan_count' => 100]);
        QrCode::factory()->create(['name' => 'Someone elses QR']);
    }

    public function test_the_page_lists_only_the_users_own_qr_codes(): void
    {
        $this->actingAs($this->owner)->get('/qr')
            ->assertOk()
            ->assertSee(['Live menu', 'Paused flyer', 'Old poster'])
            ->assertDontSee('Someone elses QR');
    }

    public function test_status_filters_include_the_derived_limit_reached_state(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(QrIndex::class)->set('status', 'active')->assertSee('Live menu')->assertDontSee(['Paused flyer', 'Old poster']);
        Livewire::test(QrIndex::class)->set('status', 'paused')->assertSee('Paused flyer')->assertDontSee(['Live menu', 'Old poster']);
        Livewire::test(QrIndex::class)->set('status', 'limit')->assertSee('Old poster')->assertDontSee(['Live menu', 'Paused flyer']);
    }

    public function test_search_matches_name_or_code(): void
    {
        $this->actingAs($this->owner);
        $code = QrCode::query()->where('name', 'Old poster')->value('code');

        Livewire::test(QrIndex::class)->set('search', 'menu')->assertSee('Live menu')->assertDontSee('Old poster');
        Livewire::test(QrIndex::class)->set('search', $code)->assertSee('Old poster')->assertDontSee('Live menu');
    }

    public function test_owner_can_pause_resume_and_delete(): void
    {
        $this->actingAs($this->owner);
        $qrCode = QrCode::query()->where('name', 'Live menu')->sole();

        Livewire::test(QrIndex::class)->call('toggleStatus', $qrCode->id)->assertDispatched('toast');
        $this->assertSame(QrStatus::Paused, $qrCode->fresh()->status);

        Livewire::test(QrIndex::class)->call('toggleStatus', $qrCode->id);
        $this->assertSame(QrStatus::Active, $qrCode->fresh()->status);

        Livewire::test(QrIndex::class)->call('delete', $qrCode->id);
        $this->assertModelMissing($qrCode);
    }

    public function test_users_cannot_pause_or_delete_other_users_qr_codes(): void
    {
        $this->actingAs($this->owner);
        $other = QrCode::query()->where('name', 'Someone elses QR')->sole();

        // Livewire answers the AuthorizationException with a 403; nothing may change.
        Livewire::test(QrIndex::class)->call('toggleStatus', $other->id)->assertNotDispatched('toast');
        Livewire::test(QrIndex::class)->call('delete', $other->id)->assertNotDispatched('toast');

        $this->assertSame(QrStatus::Active, $other->fresh()->status);
        $this->assertModelExists($other);
    }

    public function test_empty_state_for_new_users(): void
    {
        $this->actingAs(User::factory()->create())->get('/qr')->assertOk()->assertSee(__('No QR codes yet'));
    }
}
