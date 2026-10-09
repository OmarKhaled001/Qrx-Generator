<?php

namespace Tests\Feature\Qr;

use App\Enums\QrType;
use App\Livewire\Dashboard\QrBuilder;
use App\Models\Plan;
use App\Models\QrCode;
use App\Models\User;
use Database\Factories\QrCodeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\SubscribesUsers;
use Tests\TestCase;

class QrBuilderTest extends TestCase
{
    use RefreshDatabase, SubscribesUsers;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->freePlan();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * @return array<string, array{0: QrType}>
     */
    public static function types(): array
    {
        return collect(QrType::cases())->mapWithKeys(fn (QrType $type) => [$type->value => [$type]])->all();
    }

    #[DataProvider('types')]
    public function test_each_type_can_be_created(QrType $type): void
    {
        $payload = QrCodeFactory::samplePayload($type);

        Livewire::test(QrBuilder::class)
            ->call('selectType', $type->value)
            ->set('name', 'My '.$type->value)
            ->set('payload', $payload)
            ->call('save')
            ->assertHasNoErrors();

        $qrCode = QrCode::sole();
        $this->assertSame($type, $qrCode->type);
        $this->assertEquals($payload, $qrCode->payload); // key order differs on MySQL
        $this->assertSame($this->user->id, $qrCode->user_id);
    }

    public function test_the_create_page_renders_with_a_preview(): void
    {
        $this->get('/qr/create')->assertOk()->assertSee('data:image/svg+xml;base64,', false)->assertSee(__('Create QR code'));
    }

    public function test_unsafe_urls_are_rejected(): void
    {
        Livewire::test(QrBuilder::class)
            ->set('name', 'Bad')
            ->set('payload.url', 'javascript:alert(1)')
            ->call('save')
            ->assertHasErrors(['payload.url']);

        $this->assertSame(0, QrCode::count());
    }

    public function test_the_free_plan_limit_is_enforced(): void
    {
        QrCode::factory()->count(3)->for($this->user)->create();

        Livewire::test(QrBuilder::class)
            ->assertSee(__('You have reached the limit of your plan.'))
            ->set('name', 'Fourth')
            ->set('payload.url', 'https://example.com')
            ->call('save')
            ->assertHasErrors(['quota']);

        $this->assertSame(3, QrCode::count());
    }

    public function test_paid_plans_raise_the_limit(): void
    {
        QrCode::factory()->count(3)->for($this->user)->create();
        $this->subscribe($this->user, Plan::factory()->create(['qr_limit' => 25]));

        Livewire::test(QrBuilder::class)->set('name', 'Fourth')->set('payload.url', 'https://example.com')->call('save')->assertHasNoErrors();

        $this->assertSame(4, QrCode::count());
    }

    public function test_editing_updates_content_type_and_design(): void
    {
        $qrCode = QrCode::factory()->for($this->user)->ofType(QrType::Url)->create(['name' => 'Menu']);

        Livewire::test(QrBuilder::class, ['qrCode' => $qrCode])
            ->assertSet('name', 'Menu')
            ->call('selectType', 'wifi')
            ->set('payload.ssid', 'Cafe')
            ->set('payload.password', 'secret-pass')
            ->set('useGradient', true)
            ->set('design.gradient.from', '#112233')
            ->set('design.gradient.to', '#445566')
            ->set('design.style', 'dot')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('qr.show', $qrCode));

        $fresh = $qrCode->fresh();
        $this->assertSame(QrType::Wifi, $fresh->type);
        $this->assertSame('Cafe', $fresh->payload['ssid']);
        $this->assertArrayNotHasKey('url', $fresh->payload);
        // MySQL reorders JSON object keys, so compare keys and values, not order.
        $this->assertEquals(['from' => '#112233', 'to' => '#445566'], $fresh->design['gradient']);
        $this->assertSame('dot', $fresh->design['style']);
        $this->assertSame($qrCode->code, $fresh->code);
    }

    public function test_editing_at_the_plan_limit_is_still_allowed(): void
    {
        $qrCode = QrCode::factory()->for($this->user)->create();
        QrCode::factory()->count(2)->for($this->user)->create();

        Livewire::test(QrBuilder::class, ['qrCode' => $qrCode])->set('name', 'Renamed')->call('save')->assertHasNoErrors();

        $this->assertSame('Renamed', $qrCode->fresh()->name);
    }

    public function test_low_contrast_designs_show_a_warning(): void
    {
        Livewire::test(QrBuilder::class)
            ->set('design.color', '#eeeeee')
            ->assertSee(__('Low contrast between the code and the background may make it hard to scan.'));
    }

    public function test_users_cannot_edit_other_users_qr_codes(): void
    {
        $other = QrCode::factory()->create();

        $this->get('/qr/'.$other->id.'/edit')->assertForbidden();
    }
}
