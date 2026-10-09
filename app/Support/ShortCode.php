<?php

namespace App\Support;

use App\Models\QrCode;
use RuntimeException;

/**
 * Random, non-sequential short codes for /x/{code}. The alphabet leaves out
 * look-alike characters (0/O, 1/l/I) so printed codes are easy to type.
 */
final class ShortCode
{
    public const LENGTH = 8;

    private const ALPHABET = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';

    private const ATTEMPTS = 5;

    public static function generate(): string
    {
        for ($attempt = 0; $attempt < self::ATTEMPTS; $attempt++) {
            $code = self::random();

            if (! QrCode::query()->where('code', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException('Could not generate a unique short code.');
    }

    private static function random(): string
    {
        $max = strlen(self::ALPHABET) - 1;
        $code = '';

        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= self::ALPHABET[random_int(0, $max)];
        }

        return $code;
    }
}
