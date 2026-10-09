<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesQrxCodes;
use Tests\TestCase;

/**
 * Characterization of the dashboard QR lists before the v2 rewrite.
 */
class QrxCodeListTest extends TestCase
{
    use CreatesQrxCodes, RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();

        foreach (['active' => 'Live menu', 'pause' => 'Paused flyer', 'exp' => 'Old poster'] as $status => $name) {
            $this->createQrxCode($this->owner)->forceFill(['status' => $status, 'name' => $name])->save();
        }

        $this->createQrxCode(User::factory()->create())->forceFill(['name' => 'Someone elses QR'])->save();
    }

    public function test_the_all_list_shows_only_the_users_own_qrs(): void
    {
        $this->actingAs($this->owner)
            ->get(route('dashboard.qr.all'))
            ->assertOk()
            ->assertSee(['Live menu', 'Paused flyer', 'Old poster'])
            ->assertDontSee('Someone elses QR');
    }

    /**
     * @dataProvider statusLists
     */
    public function test_status_lists_only_show_that_status(string $route, string $shown, array $hidden): void
    {
        $this->actingAs($this->owner)
            ->get(route($route))
            ->assertOk()
            ->assertSee($shown)
            ->assertDontSee($hidden);
    }

    public static function statusLists(): array
    {
        return [
            'active' => ['dashboard.qr.active', 'Live menu', ['Paused flyer', 'Old poster', 'Someone elses QR']],
            'paused' => ['dashboard.qr.pause', 'Paused flyer', ['Live menu', 'Old poster', 'Someone elses QR']],
            'expired' => ['dashboard.qr.expire', 'Old poster', ['Live menu', 'Paused flyer', 'Someone elses QR']],
        ];
    }
}
