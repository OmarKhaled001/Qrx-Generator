<?php

namespace Tests\Feature\Ui;

use App\Enums\QrType;
use App\Models\Contact;
use App\Models\Plan;
use App\Models\QrCode;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Renders every page in Arabic and fails on any English word left in the visible text
 * (or in aria-label, title, placeholder and alt). User content in the fixtures is Arabic,
 * so anything Latin that remains comes from the app itself.
 */
class ArabicPagesTest extends TestCase
{
    use RefreshDatabase;

    // Brand and technical tokens written the same way in Arabic text.
    private const ALLOWED = ['Qrx', 'QR', 'PNG', 'SVG', 'EPS', 'WPA', 'WEP', 'IP', 'vCard', 'Google', 'Apple', 'Facebook', 'Stripe', 'English', 'EN', 'AR'];

    public function test_public_and_auth_pages_have_no_english_left(): void
    {
        $this->seed(PlanSeeder::class);
        $owner = User::factory()->create(['name' => 'ليلى حسن', 'locale' => 'ar']);
        $codes = collect(QrType::cases())->mapWithKeys(fn (QrType $type) => [$type->value => QrCode::factory()->for($owner)->ofType($type)->create(['name' => 'رمز تجريبي'])]);
        $codes['text']->update(['payload' => ['text' => 'مرحبًا بكم في متجرنا']]);
        $codes['vcard']->update(['payload' => ['full_name' => 'ليلى حسن', 'organization' => 'المتجر', 'title' => 'مديرة', 'phone' => '+201000000001', 'email' => 'layla@example.com', 'note' => 'ملاحظة']]);
        $codes['email']->update(['payload' => ['email' => 'layla@example.com', 'subject' => 'استفسار', 'body' => 'مرحبًا']]);
        $codes['sms']->update(['payload' => ['phone' => '+201000000001', 'message' => 'أريد الحجز']]);
        $codes['wifi']->update(['payload' => ['ssid' => 'شبكة-المتجر', 'password' => '12345678', 'encryption' => 'WPA', 'hidden' => false]]);
        $codes['location']->update(['payload' => ['latitude' => '30.0444', 'longitude' => '31.2357', 'label' => 'برج القاهرة']]);
        $paused = QrCode::factory()->for($owner)->paused()->create(['name' => 'رمز متوقف']);

        $pages = ['/', '/login', '/register', '/forgot-password', '/reset-password/token?email=layla@example.com', '/x/missing00', '/x/'.$paused->code, '/no-such-page'];
        foreach ($codes as $type => $qrCode) {
            if ($type !== 'url') {
                $pages[] = '/x/'.$qrCode->code;
            }
        }

        foreach ($pages as $url) {
            $this->assertNoEnglish($this->withSession(['locale' => 'ar'])->get($url)->getContent(), $url);
        }

        $this->actingAs($owner);
        foreach (['/dashboard', '/qr', '/qr/create', '/qr/'.$codes['vcard']->id, '/qr/'.$codes['wifi']->id.'/edit', '/billing', '/profile'] as $url) {
            $this->assertNoEnglish($this->get($url)->assertOk()->getContent(), $url);
        }
    }

    public function test_admin_pages_have_no_english_left(): void
    {
        $this->seed(PlanSeeder::class);
        $admin = User::factory()->admin()->create(['name' => 'مدير النظام', 'locale' => 'ar']);
        $qrCode = QrCode::factory()->for(User::factory()->create(['name' => 'سارة']))->create(['name' => 'قائمة الطعام']);
        Contact::query()->create(['name' => 'سارة', 'email' => 'sara@example.com', 'subject' => 'سؤال', 'message' => 'مرحبًا']);
        $plan = Plan::query()->where('slug', 'starter')->sole();

        $this->actingAs($admin);
        foreach (['/admin', '/admin/users', '/admin/users/create', '/admin/users/'.$admin->id.'/edit', '/admin/qr-codes', '/admin/qr-codes/'.$qrCode->id.'/edit', '/admin/plans', '/admin/plans/create', '/admin/plans/'.$plan->id.'/edit', '/admin/contacts'] as $url) {
            $this->assertNoEnglish($this->get($url)->assertOk()->getContent(), $url);
        }
    }

    private function assertNoEnglish(string $html, string $url): void
    {
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8">'.$html, LIBXML_NOERROR);
        $xpath = new DOMXPath($dom);

        // Code, drawings and anything marked translate="no" (identifiers such as slugs) are not language.
        foreach (iterator_to_array($xpath->query('//script | //style | //svg | //template | //*[@translate="no"]')) as $node) {
            $node->parentNode?->removeChild($node);
        }

        $text = $dom->textContent;
        foreach ($xpath->query('//@aria-label | //@title | //@placeholder | //@alt') as $attribute) {
            $text .= ' '.$attribute->value;
        }

        // Addresses, links, file paths, identifiers (price_…), colours and short codes are data, not language.
        $text = preg_replace(['~\S+@\S+~', '~https?://\S*~', '~\S*/\S*~', '~\b\w+_\S*~u', '~#[0-9A-Fa-f]{3,8}\b~', '~\b\w*\d\w*\b~u'], ' ', $text);

        preg_match_all('~[A-Za-z][A-Za-z\'’-]*~', $text, $words);
        $english = array_values(array_unique(array_filter($words[0], fn (string $word) => ! in_array($word, self::ALLOWED, true))));

        $this->assertSame([], $english, "English left on {$url} in Arabic: ".implode(', ', $english));
    }
}
