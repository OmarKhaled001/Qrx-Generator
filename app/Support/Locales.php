<?php

namespace App\Support;

final class Locales
{
    /**
     * @return list<string>
     */
    public static function supported(): array
    {
        return array_keys(config('qrx.locales'));
    }

    public static function isSupported(?string $locale): bool
    {
        return $locale !== null && in_array($locale, self::supported(), true);
    }

    public static function dir(?string $locale = null): string
    {
        return config('qrx.locales.'.($locale ?? app()->getLocale()).'.dir', 'ltr');
    }

    public static function isRtl(?string $locale = null): bool
    {
        return self::dir($locale) === 'rtl';
    }

    public static function name(string $locale): string
    {
        return config("qrx.locales.{$locale}.name", $locale);
    }
}
