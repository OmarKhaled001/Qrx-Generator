<?php

namespace App\Enums;

/**
 * Stored status. "Limit reached" is not stored: it is derived from the owner's
 * current plan, so upgrading brings a QR back without touching every row.
 */
enum QrStatus: string
{
    case Active = 'active';
    case Paused = 'paused';

    public function label(): string
    {
        return match ($this) {
            self::Active => __('Active'),
            self::Paused => __('Paused'),
        };
    }
}
