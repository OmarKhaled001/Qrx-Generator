<?php

namespace Tests\Feature;

use App\Enums\QrType;
use App\Livewire\Dashboard\QrBuilder;
use App\Models\QrCode;
use App\Models\Scan;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The MVP journey in one test: sign up, verify, create a QR code, a phone scans
 * it, change where it points, download it; then the same in Arabic.
 */
class EndToEndJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_whole_customer_journey(): void
    {
        Notification::fake();
        $this->seed(PlanSeeder::class);

        // 1. Sign up from the landing page.
        $this->get('/')->assertOk()->assertSee(__('Start free'));
        $this->post('/register', [
            'name' => 'Layla Hassan', 'email' => 'layla@example.com', 'phone' => '+20 100 123 4567',
            'country' => 'Egypt', 'city' => 'Alexandria', 'area' => 'Smouha',
            'password' => 'correct-horse-9', 'password_confirmation' => 'correct-horse-9',
        ])->assertRedirect('/dashboard');
        $user = User::query()->where('email', 'layla@example.com')->sole();

        // 2. Verify the email address.
        $this->get('/dashboard')->assertRedirect('/email/verify');
        Notification::assertSentTo($user, VerifyEmail::class);
        $this->get(URL::temporarySignedRoute('verification.verify', now()->addHour(), ['id' => $user->id, 'hash' => sha1($user->email)]));
        $this->get('/dashboard')->assertOk()->assertSee(__('No QR codes yet'));
        $user->refresh();

        // 3. Create a website QR code in the builder.
        Livewire::test(QrBuilder::class)
            ->set('name', 'Spring menu')
            ->set('payload.url', 'https://example.com/spring')
            ->set('design.style', 'round')
            ->call('save')
            ->assertHasNoErrors();
        $qrCode = QrCode::query()->where('name', 'Spring menu')->sole();
        $this->get('/qr')->assertOk()->assertSee('Spring menu');

        // 4. A phone scans the printed code.
        $phone = ['HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) Mobile/15E148'];
        auth()->logout();
        $this->withServerVariables($phone)->get('/x/'.$qrCode->code)->assertRedirect('https://example.com/spring');

        // 5. The owner points it somewhere new; the same printed code follows.
        $this->actingAs($user);
        Livewire::test(QrBuilder::class, ['qrCode' => $qrCode])->set('payload.url', 'https://example.com/summer')->call('save')->assertHasNoErrors();
        auth()->logout();
        $this->withServerVariables($phone)->get('/x/'.$qrCode->code)->assertRedirect('https://example.com/summer');

        $this->assertSame(2, $qrCode->fresh()->scan_count);
        $this->assertSame(2, Scan::query()->where('qr_code_id', $qrCode->id)->count());

        // 6. Download a print-ready PNG.
        $this->actingAs($user)->get("/qr/{$qrCode->id}/download?format=png&size=1000")->assertOk()->assertHeader('Content-Type', 'image/png');
        $this->get("/qr/{$qrCode->id}")->assertOk()->assertSee([__('Total scans'), $qrCode->shortUrl()]);
    }

    public function test_the_journey_in_arabic_is_right_to_left(): void
    {
        $this->seed(PlanSeeder::class);
        $user = User::factory()->create(['locale' => 'ar']);
        $qrCode = QrCode::factory()->for($user)->ofType(QrType::Wifi)->create(['name' => 'شبكة المقهى']);

        $this->actingAs($user);
        foreach (['/dashboard', '/qr', '/qr/create', "/qr/{$qrCode->id}", '/billing', '/profile'] as $page) {
            $this->get($page)->assertOk()->assertSee('<html lang="ar" dir="rtl">', false);
        }

        auth()->logout();
        $this->withSession(['locale' => 'ar'])->get('/x/'.$qrCode->code)->assertOk()->assertSee('dir="rtl"', false)->assertSee('شبكة المقهى');
    }
}
