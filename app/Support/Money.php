<?php

namespace App\Support;

use Illuminate\Support\Number;

/**
 * Prices read the same in every language ("$29"). ICU's Arabic format ("US$ 29") mixes Latin
 * letters into Arabic text and the bidi algorithm reorders it into "$US 29".
 */
final class Money
{
    public static function format(int $cents, string $currency = 'usd'): string
    {
        return Number::currency($cents / 100, in: strtoupper($currency), locale: 'en', precision: $cents % 100 === 0 ? 0 : 2);
    }
}
