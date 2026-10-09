<?php

namespace Tests\Feature;

use App\Http\Livewire\QrxCreate;
use App\Models\FeatureEmail;
use App\Models\FeatureMessage;
use App\Models\QrxCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Tests\Concerns\CreatesQrxCodes;
use Tests\TestCase;

// Creating a QR renders the stored PNG, which simple-qrcode does through imagick.
#[RequiresPhpExtension('imagick')]
class QrxCodeCreationTest extends TestCase
{
    use CreatesQrxCodes, RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->user = User::factory()->create();
    }

    public function test_subscriber_can_create_a_text_qr(): void
    {
        $this->subscribe($this->user);
        $this->actingAs($this->user);

        Livewire::test(QrxCreate::class)
            ->set('qrxName', 'Menu')
            ->set('text', 'Hello world')
            ->set('color', '#112233')
            ->call('generat')
            ->assertRedirect(route('dashboard.qr.all'));

        $qrxCode = QrxCode::sole();
        $this->assertSame('Menu', $qrxCode->name);
        $this->assertSame('text', $qrxCode->type);
        $this->assertSame($this->user->id, (int) $qrxCode->user_id);
        $this->assertSame('qrxs/'.$qrxCode->code.'.png', $qrxCode->path);
        $this->assertSame('Hello world', $qrxCode->text->text);
        $this->assertSame('#112233', $qrxCode->style->color);
        Storage::disk('public')->assertExists($qrxCode->path);
    }

    public function test_email_qr_only_creates_an_email_feature(): void
    {
        $this->subscribe($this->user);
        $this->actingAs($this->user);

        Livewire::test(QrxCreate::class)
            ->call('email')
            ->set('qrxName', 'Support')
            ->set('email', 'support@example.com')
            ->set('subject', 'Help')
            ->set('body', 'Hi')
            ->call('generat');

        $this->assertSame(1, FeatureEmail::count());
        $this->assertSame(0, FeatureMessage::count());
    }

    public function test_user_without_a_subscription_is_sent_to_the_plans_page(): void
    {
        $this->actingAs($this->user);

        Livewire::test(QrxCreate::class)
            ->set('qrxName', 'Menu')
            ->set('text', 'Hello')
            ->call('generat')
            ->assertRedirect(route('dashboard.error.plan.subscripe'));

        $this->assertSame(0, QrxCode::count());
    }

    public function test_user_at_their_plan_limit_is_sent_to_the_upgrade_page(): void
    {
        $this->subscribe($this->user, qrCount: 1);
        $this->createQrxCode($this->user);
        $this->actingAs($this->user);

        Livewire::test(QrxCreate::class)
            ->set('qrxName', 'One too many')
            ->set('text', 'Hello')
            ->call('generat')
            ->assertRedirect(route('dashboard.error.plan.upgrade'));

        $this->assertSame(1, QrxCode::count());
    }
}
