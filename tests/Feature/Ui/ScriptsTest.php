<?php

namespace Tests\Feature\Ui;

use App\Enums\QrType;
use App\Models\QrCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\SubscribesUsers;
use Tests\TestCase;

/**
 * Menus, dropdowns and the theme toggle run on Alpine, which Livewire ships. Livewire only
 * injects it on pages with a component, so the layouts load it themselves: once, everywhere.
 * One page per test: Livewire renders its scripts once per application instance.
 */
class ScriptsTest extends TestCase
{
    use RefreshDatabase, SubscribesUsers;

    /**
     * @return array<string, array{string, bool}>
     */
    public static function pages(): array
    {
        return [
            'landing (has a component)' => ['/', false],
            'login (guest layout)' => ['/login', false],
            'scan page (public layout)' => ['/x/{code}', false],
            'dashboard (app layout)' => ['/dashboard', true],
            'builder (has a component)' => ['/qr/create', true],
        ];
    }

    #[DataProvider('pages')]
    public function test_every_layout_loads_alpine_exactly_once(string $url, bool $signedIn): void
    {
        $this->freePlan();
        $qrCode = QrCode::factory()->ofType(QrType::Text)->create();

        if ($signedIn) {
            $this->actingAs($qrCode->user);
        }

        $html = $this->get(str_replace('{code}', $qrCode->code, $url))->assertOk()->getContent();

        $this->assertSame(1, preg_match_all('~/livewire(\.min)?\.js~', $html));
    }
}
