<?php

namespace Tests\Feature\Domain;

use App\Enums\QrType;
use App\Support\QrDesign;
use Database\Factories\QrCodeFactory;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class QrTypeValidationTest extends TestCase
{
    /**
     * @return array<string, array{0: QrType}>
     */
    public static function types(): array
    {
        return collect(QrType::cases())->mapWithKeys(fn (QrType $type) => [$type->value => [$type]])->all();
    }

    #[DataProvider('types')]
    public function test_the_sample_payload_of_every_type_is_valid(QrType $type): void
    {
        $validator = Validator::make(['payload' => QrCodeFactory::samplePayload($type)], $type->rules());

        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
    }

    #[DataProvider('types')]
    public function test_an_empty_payload_of_every_type_is_rejected(QrType $type): void
    {
        $this->assertTrue(Validator::make(['payload' => $type->defaults()], $type->rules())->fails());
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function unsafeUrls(): array
    {
        return [
            'javascript' => ['javascript:alert(document.cookie)'],
            'mixed-case javascript' => ['JaVaScRiPt:alert(1)'],
            'data uri' => ['data:text/html;base64,PHNjcmlwdD4='],
            'vbscript' => ['vbscript:msgbox(1)'],
            'file' => ['file:///etc/passwd'],
            'no scheme' => ['example.com'],
        ];
    }

    #[DataProvider('unsafeUrls')]
    public function test_url_qrs_only_accept_http_and_https(string $url): void
    {
        $this->assertTrue(Validator::make(['payload' => ['url' => $url]], QrType::Url->rules())->fails());
        $this->assertTrue(Validator::make(['payload' => ['full_name' => 'A', 'website' => $url]], QrType::Vcard->rules())->fails());
    }

    public function test_wifi_password_is_optional_only_for_open_networks(): void
    {
        $rules = QrType::Wifi->rules();

        $this->assertTrue(Validator::make(['payload' => ['ssid' => 'Cafe', 'encryption' => 'WPA', 'password' => '']], $rules)->fails());
        $this->assertFalse(Validator::make(['payload' => ['ssid' => 'Cafe', 'encryption' => 'nopass', 'password' => '']], $rules)->fails());
    }

    public function test_location_coordinates_must_be_on_earth(): void
    {
        $rules = QrType::Location->rules();

        $this->assertTrue(Validator::make(['payload' => ['latitude' => 91, 'longitude' => 0]], $rules)->fails());
        $this->assertTrue(Validator::make(['payload' => ['latitude' => 0, 'longitude' => 181]], $rules)->fails());
        $this->assertFalse(Validator::make(['payload' => ['latitude' => -33.86, 'longitude' => 151.21]], $rules)->fails());
    }

    public function test_clean_keeps_only_known_fields_and_trims(): void
    {
        $clean = QrType::Url->clean(['url' => '  https://example.com  ', 'evil' => 'x']);

        $this->assertSame(['url' => 'https://example.com'], $clean);
        $this->assertFalse(QrType::Wifi->clean(['ssid' => 'x'])['hidden']);
    }

    public function test_design_rules_reject_bad_values_and_normalize_clamps(): void
    {
        $bad = ['style' => 'blob', 'eye' => 'square', 'color' => 'red', 'bg' => '#ffffff', 'margin' => 50];
        $this->assertCount(3, Validator::make(['design' => $bad], QrDesign::rules())->errors());

        $normalized = QrDesign::normalize(['style' => 'blob', 'margin' => 50, 'color' => '#ABCDEF', 'gradient' => ['from' => '#000000']]);
        $this->assertSame('square', $normalized['style']);
        $this->assertSame(10, $normalized['margin']);
        $this->assertSame('#abcdef', $normalized['color']);
        $this->assertNull($normalized['gradient']);
    }
}
