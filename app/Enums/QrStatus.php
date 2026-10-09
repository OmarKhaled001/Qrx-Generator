<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Stored status. "Limit reached" is not stored: it is derived from the owner's
 * current plan, so upgrading brings a QR back without touching every row.
 */
enum QrStatus: string implements HasColor, HasLabel
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

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getColor(): string
    {
        return $this === self::Active ? 'success' : 'warning';
    }
}
