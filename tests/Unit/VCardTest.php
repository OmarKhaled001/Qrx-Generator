<?php

namespace Tests\Unit;

use App\Support\VCard;
use PHPUnit\Framework\TestCase;

class VCardTest extends TestCase
{
    public function test_it_builds_a_vcard_3_with_crlf_line_endings(): void
    {
        $vcf = VCard::fromPayload(['full_name' => 'Jane Doe', 'phone' => '+201000000001', 'email' => 'jane@example.com']);

        $this->assertSame(
            "BEGIN:VCARD\r\nVERSION:3.0\r\nFN:Jane Doe\r\nN:Jane Doe;;;;\r\nTEL;TYPE=CELL:+201000000001\r\nEMAIL;TYPE=INTERNET:jane@example.com\r\nEND:VCARD\r\n",
            $vcf,
        );
    }

    public function test_it_escapes_special_characters_and_skips_empty_fields(): void
    {
        $vcf = VCard::fromPayload(['full_name' => 'Doe; Jane', 'note' => "Line one\nLine, two", 'title' => '']);

        $this->assertStringContainsString('FN:Doe\; Jane', $vcf);
        $this->assertStringContainsString('NOTE:Line one\nLine\, two', $vcf);
        $this->assertStringNotContainsString('TITLE', $vcf);
    }

    public function test_arabic_names_are_kept_as_utf8(): void
    {
        $this->assertStringContainsString('FN:ليلى حسن', VCard::fromPayload(['full_name' => 'ليلى حسن']));
    }
}
