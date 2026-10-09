<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

/**
 * Every QR code points at its short URL (/x/{code}); the type decides what the
 * scan page shows and which payload fields are stored and validated.
 */
enum QrType: string implements HasIcon, HasLabel
{
    case Url = 'url';
    case Text = 'text';
    case Phone = 'phone';
    case Sms = 'sms';
    case Email = 'email';
    case Vcard = 'vcard';
    case Wifi = 'wifi';
    case Location = 'location';

    private const PHONE = 'regex:/^\+?[0-9\s\-().]{5,30}$/';

    public function label(): string
    {
        return match ($this) {
            self::Url => __('Website'),
            self::Text => __('Text'),
            self::Phone => __('Phone call'),
            self::Sms => __('SMS'),
            self::Email => __('Email'),
            self::Vcard => __('Contact card'),
            self::Wifi => __('Wi-Fi'),
            self::Location => __('Location'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Url => __('Send people to any web page.'),
            self::Text => __('Show a short message.'),
            self::Phone => __('Start a phone call.'),
            self::Sms => __('Open a prefilled text message.'),
            self::Email => __('Open a prefilled email.'),
            self::Vcard => __('Share contact details people can save.'),
            self::Wifi => __('Share your network name and password.'),
            self::Location => __('Point to a place on the map.'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Url => 'heroicon-o-link',
            self::Text => 'heroicon-o-document-text',
            self::Phone => 'heroicon-o-phone',
            self::Sms => 'heroicon-o-chat-bubble-left-ellipsis',
            self::Email => 'heroicon-o-envelope',
            self::Vcard => 'heroicon-o-identification',
            self::Wifi => 'heroicon-o-wifi',
            self::Location => 'heroicon-o-map-pin',
        };
    }

    /**
     * Empty payload for a new QR code of this type.
     *
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return match ($this) {
            self::Url => ['url' => ''],
            self::Text => ['text' => ''],
            self::Phone => ['phone' => ''],
            self::Sms => ['phone' => '', 'message' => ''],
            self::Email => ['email' => '', 'subject' => '', 'body' => ''],
            self::Vcard => [
                'full_name' => '', 'organization' => '', 'title' => '', 'phone' => '', 'tel' => '',
                'email' => '', 'website' => '', 'address' => '', 'note' => '',
            ],
            self::Wifi => ['ssid' => '', 'password' => '', 'encryption' => 'WPA', 'hidden' => false],
            self::Location => ['latitude' => '', 'longitude' => '', 'label' => ''],
        };
    }

    /**
     * Keep only this type's fields, trimmed; anything else in the input is dropped.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function clean(array $payload): array
    {
        $clean = [];

        foreach ($this->defaults() as $field => $default) {
            $value = $payload[$field] ?? $default;
            $clean[$field] = match (true) {
                is_bool($default) => (bool) $value,
                is_string($value) => trim($value),
                default => $value,
            };
        }

        return $clean;
    }

    /**
     * Validation rules for the payload, keyed by "{prefix}{field}".
     *
     * @return array<string, array<int, string>>
     */
    public function rules(string $prefix = 'payload.'): array
    {
        $rules = match ($this) {
            self::Url => [
                'url' => ['required', 'string', 'max:2048', 'url:http,https'],
            ],
            self::Text => [
                'text' => ['required', 'string', 'max:1000'],
            ],
            self::Phone => [
                'phone' => ['required', 'string', self::PHONE],
            ],
            self::Sms => [
                'phone' => ['required', 'string', self::PHONE],
                'message' => ['nullable', 'string', 'max:500'],
            ],
            self::Email => [
                'email' => ['required', 'email', 'max:255'],
                'subject' => ['nullable', 'string', 'max:255'],
                'body' => ['nullable', 'string', 'max:2000'],
            ],
            self::Vcard => [
                'full_name' => ['required', 'string', 'max:120'],
                'organization' => ['nullable', 'string', 'max:120'],
                'title' => ['nullable', 'string', 'max:120'],
                'phone' => ['nullable', 'string', self::PHONE],
                'tel' => ['nullable', 'string', self::PHONE],
                'email' => ['nullable', 'email', 'max:255'],
                'website' => ['nullable', 'string', 'max:2048', 'url:http,https'],
                'address' => ['nullable', 'string', 'max:255'],
                'note' => ['nullable', 'string', 'max:500'],
            ],
            self::Wifi => [
                'ssid' => ['required', 'string', 'max:64'],
                'encryption' => ['required', 'in:WPA,WEP,nopass'],
                'password' => ["required_unless:{$prefix}encryption,nopass", 'nullable', 'string', 'max:64'],
                'hidden' => ['boolean'],
            ],
            self::Location => [
                'latitude' => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],
                'label' => ['nullable', 'string', 'max:120'],
            ],
        };

        return collect($rules)->mapWithKeys(fn (array $fieldRules, string $field) => [$prefix.$field => $fieldRules])->all();
    }

    /**
     * Human-readable names for the payload fields, for validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(string $prefix = 'payload.'): array
    {
        $names = [
            'url' => __('Website address'), 'text' => __('Text'), 'phone' => __('Phone number'),
            'message' => __('Message'), 'email' => __('Email address'), 'subject' => __('Subject'),
            'body' => __('Body'), 'full_name' => __('Full name'), 'organization' => __('Organization'),
            'title' => __('Job title'), 'tel' => __('Work phone'), 'website' => __('Website'),
            'address' => __('Address'), 'note' => __('Note'), 'ssid' => __('Network name'),
            'password' => __('Password'), 'encryption' => __('Security'), 'hidden' => __('Hidden network'),
            'latitude' => __('Latitude'), 'longitude' => __('Longitude'), 'label' => __('Place name'),
        ];

        return collect(array_keys($this->defaults()))
            ->mapWithKeys(fn (string $field) => [$prefix.$field => $names[$field] ?? $field])
            ->all();
    }

    /**
     * One-line description of a payload, for lists.
     *
     * @param  array<string, mixed>  $payload
     */
    public function summary(array $payload): string
    {
        $summary = match ($this) {
            self::Url => $payload['url'] ?? '',
            self::Text => $payload['text'] ?? '',
            self::Phone, self::Sms => $payload['phone'] ?? '',
            self::Email => $payload['email'] ?? '',
            self::Vcard => $payload['full_name'] ?? '',
            self::Wifi => $payload['ssid'] ?? '',
            self::Location => ($payload['label'] ?? '') ?: (($payload['latitude'] ?? '').', '.($payload['longitude'] ?? '')),
        };

        return Str::limit((string) $summary, 60);
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getIcon(): string
    {
        return $this->icon();
    }
}
