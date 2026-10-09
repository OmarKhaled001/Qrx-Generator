<?php

namespace App\Support;

/**
 * Builds a vCard 3.0 (.vcf) from a contact-card QR payload so phones can save it.
 */
final class VCard
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): string
    {
        $value = fn (string $key): string => self::escape(trim((string) ($payload[$key] ?? '')));

        $lines = ['BEGIN:VCARD', 'VERSION:3.0', 'FN:'.$value('full_name'), 'N:'.$value('full_name').';;;;'];

        foreach ([
            'organization' => 'ORG',
            'title' => 'TITLE',
            'phone' => 'TEL;TYPE=CELL',
            'tel' => 'TEL;TYPE=WORK',
            'email' => 'EMAIL;TYPE=INTERNET',
            'website' => 'URL',
            'address' => 'ADR;TYPE=WORK:;;',
            'note' => 'NOTE',
        ] as $field => $property) {
            if ($value($field) !== '') {
                $lines[] = (str_ends_with($property, ';;') ? $property : $property.':').$value($field);
            }
        }

        $lines[] = 'END:VCARD';

        return implode("\r\n", $lines)."\r\n";
    }

    private static function escape(string $value): string
    {
        return str_replace(['\\', "\r\n", "\n", ',', ';'], ['\\\\', '\\n', '\\n', '\\,', '\\;'], $value);
    }
}
