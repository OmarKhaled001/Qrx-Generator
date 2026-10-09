<?php

namespace Tests\Feature;

use App\Http\Livewire\QrxCreate;
use App\Models\QrxCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Tests\Concerns\CreatesQrxCodes;
use Tests\TestCase;

/**
 * Characterization: creating every QR type, and PNG/EPS downloads, before the v2 rewrite.
 * Both render through imagick.
 */
#[RequiresPhpExtension('imagick')]
class QrxCodeTypesAndDownloadTest extends TestCase
{
    use CreatesQrxCodes, RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->user = User::factory()->create();
        $this->subscribe($this->user);
        $this->actingAs($this->user);
    }

    /**
     * @dataProvider typeInputs
     */
    public function test_each_type_stores_its_content(string $tab, array $input, string $relation, array $expected): void
    {
        $component = Livewire::test(QrxCreate::class)->call($tab)->set('qrxName', 'My '.$tab);

        foreach ($input as $property => $value) {
            $component->set($property, $value);
        }

        $component->call('generat')->assertHasNoErrors()->assertRedirect(route('dashboard.qr.all'));

        $qrxCode = QrxCode::sole();
        $this->assertSame($tab, $qrxCode->type);
        foreach ($expected as $column => $value) {
            $this->assertSame($value, $qrxCode->{$relation}->{$column}, $column);
        }
    }

    public static function typeInputs(): array
    {
        return [
            'url' => ['url', ['url' => 'https://example.com/menu'], 'url', ['url' => 'https://example.com/menu']],
            'phone' => ['phone', ['phone' => '+201000000000'], 'phone', ['phone' => '+201000000000']],
            'sms' => ['message', ['phone' => '+201000000000', 'message' => 'Hello'], 'message', ['phone' => '+201000000000', 'message' => 'Hello']],
            'vcard' => ['vcard', [
                'fullName' => 'Jane Doe', 'email' => 'jane@example.com', 'phone' => '+201000000001',
                'tel' => '+20220000000', 'address' => 'Cairo', 'note' => 'Hi',
            ], 'vcard', ['full_name' => 'Jane Doe', 'email' => 'jane@example.com', 'phone' => '+201000000001', 'tel' => '+20220000000']],
            'wifi' => ['wifi', ['wireless' => 'Office', 'password' => 'secret-pass', 'encryption' => 'WEP'], 'wifi', [
                'wireless_ssid' => 'Office', 'password' => 'secret-pass', 'encryption' => 'WEP',
            ]],
        ];
    }

    /**
     * @dataProvider downloadFormats
     */
    public function test_downloads_in_each_format(string $type, string $contentType, string $magic): void
    {
        $qrxCode = $this->createQrxCode($this->user);

        $response = $this->get(route('dashboard.qr.download', ['id' => $qrxCode->id, 'type' => $type, 'size' => 300]))
            ->assertOk()
            ->assertHeader('Content-Type', $contentType);

        $this->assertStringStartsWith($magic, $response->streamedContent());
    }

    public static function downloadFormats(): array
    {
        return [
            'png' => ['png', 'image/png', "\x89PNG"],
            'eps' => ['eps', 'application/postscript', '%!PS-Adobe'],
        ];
    }
}
