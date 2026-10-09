<?php

namespace Database\Factories;

use App\Enums\QrStatus;
use App\Enums\QrType;
use App\Models\QrCode;
use App\Models\User;
use App\Support\QrDesign;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QrCode>
 */
class QrCodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'type' => QrType::Url,
            'payload' => ['url' => 'https://example.com/'.fake()->slug(2)],
            'design' => QrDesign::defaults(),
            'status' => QrStatus::Active,
        ];
    }

    /**
     * A QR code of the given type with a realistic, valid payload.
     */
    public function ofType(QrType $type): static
    {
        return $this->state(fn () => ['type' => $type, 'payload' => self::samplePayload($type)]);
    }

    public function paused(): static
    {
        return $this->state(fn () => ['status' => QrStatus::Paused]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function samplePayload(QrType $type): array
    {
        return $type->clean(match ($type) {
            QrType::Url => ['url' => 'https://example.com/menu'],
            QrType::Text => ['text' => 'Welcome to our shop!'],
            QrType::Phone => ['phone' => '+201000000000'],
            QrType::Sms => ['phone' => '+201000000000', 'message' => 'Hi there'],
            QrType::Email => ['email' => 'hello@example.com', 'subject' => 'Hello', 'body' => 'Body text'],
            QrType::Vcard => [
                'full_name' => 'Jane Doe', 'organization' => 'Qrx', 'title' => 'Designer',
                'phone' => '+201000000001', 'email' => 'jane@example.com', 'website' => 'https://example.com',
                'address' => 'Cairo, Egypt', 'note' => 'Met at the expo',
            ],
            QrType::Wifi => ['ssid' => 'Office', 'password' => 'secret-pass', 'encryption' => 'WPA'],
            QrType::Location => ['latitude' => '30.0444', 'longitude' => '31.2357', 'label' => 'Cairo Tower'],
        });
    }
}
