<?php

namespace Tests\Feature\Public;

use App\Filament\Resources\Contacts\Pages\ManageContacts;
use App\Livewire\Public\ContactForm;
use App\Livewire\Public\QrDemo;
use App\Models\Contact;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class MarketingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('qr-demo:127.0.0.1');
        RateLimiter::clear('contact:127.0.0.1');
    }

    public function test_the_landing_page_has_the_demo_and_contact_form(): void
    {
        $this->get('/')->assertOk()->assertSee([__('Try it now'), __('Questions? Talk to us')])->assertSeeLivewire(QrDemo::class);
    }

    public function test_the_demo_previews_and_downloads_without_storing_anything(): void
    {
        Livewire::test(QrDemo::class)
            ->set('text', 'Hello Qrx')
            ->assertSee('data:image/svg+xml;base64,', false)
            ->call('download')
            ->assertFileDownloaded('qrx-demo.svg');

        $this->assertDatabaseCount('qr_codes', 0);
    }

    public function test_the_demo_is_rate_limited_per_ip(): void
    {
        for ($i = 0; $i < config('qrx.demo.requests_per_minute'); $i++) {
            RateLimiter::hit('qr-demo:127.0.0.1', 60);
        }

        Livewire::test(QrDemo::class)->set('text', 'again')->assertSet('limited', true)->assertSee(__('You are going a bit fast. Wait a minute and try again.'));
    }

    public function test_a_contact_message_is_stored(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', 'Omar')->set('email', 'omar@example.com')->set('subject', 'Bulk codes')->set('message', 'We need 300 codes for an event.')
            ->call('send')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        $this->assertDatabaseHas('contacts', ['email' => 'omar@example.com', 'subject' => 'Bulk codes', 'locale' => 'en']);
    }

    public function test_bots_filling_the_honeypot_are_ignored(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', 'Bot')->set('email', 'bot@example.com')->set('subject', 'Buy')->set('message', 'Cheap links for you, click now.')
            ->set('website', 'https://spam.example')
            ->call('send')
            ->assertSet('sent', true);

        $this->assertDatabaseCount('contacts', 0);
    }

    public function test_contact_validation_and_throttle(): void
    {
        Livewire::test(ContactForm::class)->set('email', 'not-an-email')->call('send')->assertHasErrors(['name', 'email', 'subject', 'message']);

        $form = fn () => Livewire::test(ContactForm::class)->set('name', 'A')->set('email', 'a@example.com')->set('subject', 'Hi')->set('message', 'A message long enough.');
        foreach (range(1, 3) as $i) {
            $form()->call('send')->assertHasNoErrors();
        }
        $form()->call('send')->assertHasErrors(['message']);
        $this->assertDatabaseCount('contacts', 3);
    }

    public function test_admins_read_contact_messages(): void
    {
        Contact::query()->create(['name' => 'Omar', 'email' => 'omar@example.com', 'subject' => 'Bulk codes', 'message' => 'We need 300 codes.']);
        $this->actingAs(User::factory()->admin()->create());
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->get('/admin/contacts')->assertOk()->assertSee('Bulk codes');
        Livewire::test(ManageContacts::class)->callTableAction('markRead', Contact::sole());
        $this->assertNotNull(Contact::sole()->read_at);
    }
}
