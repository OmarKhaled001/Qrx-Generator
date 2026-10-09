<?php

namespace Tests\Feature\Ui;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Every user-facing string in a view must go through __() so it can be translated.
 */
class HardcodedStringTest extends TestCase
{
    // Brand names and technical tokens that are the same in every language.
    private const ALLOWED = ['Qrx', 'WPA', 'WEP', 'SVG', 'PNG', 'EPS', 'vCard', 'Wi-Fi'];

    private const TEXT_ATTRIBUTES = 'placeholder|aria-label|title|alt|label|heading|description|hint';

    public function test_views_have_no_untranslated_text(): void
    {
        $violations = [];

        foreach (File::allFiles(resource_path('views')) as $file) {
            if (str_starts_with($file->getRelativePathname(), 'vendor')) {
                continue;
            }

            $source = $this->stripCode(file_get_contents($file->getPathname()));

            preg_match_all('/(?<![:\w.-])(?:'.self::TEXT_ATTRIBUTES.')="([^"]*\p{L}[^"]*)"/u', $source, $attributes);
            foreach ($attributes[1] as $value) {
                if (! str_contains($value, '{{') && ! $this->allowed($value)) {
                    $violations[] = $file->getRelativePathname().'  attribute: '.$value;
                }
            }

            // Directives first (their arguments may contain "=>"), then tags with quote-aware attributes.
            $text = preg_replace('/@\w+\s*(\((?:[^()]++|(?1))*\))?/', ' ', $source);
            $text = preg_replace('/<(?:[^>"\']|"[^"]*"|\'[^\']*\')*>/s', ' ', $text);

            preg_match_all('/\p{L}[\p{L}\p{M}\'’-]+/u', $text, $words);
            foreach (array_unique($words[0]) as $word) {
                if (! $this->allowed($word)) {
                    $violations[] = $file->getRelativePathname().'  text: '.$word;
                }
            }
        }

        $this->assertSame([], $violations, "Wrap user-facing text in __():\n".implode("\n", $violations));
    }

    private function stripCode(string $source): string
    {
        $patterns = [
            '/\{\{--.*?--\}\}/s',
            '/@php\b(?!\s*\().*?@endphp/s',
            '/@php\s*(\((?:[^()]++|(?1))*\))/',
            '/<script\b.*?<\/script>/s',
            '/<style\b.*?<\/style>/s',
            '/<svg\b.*?<\/svg>/s',
            '/\{\{.*?\}\}/s',
            '/\{!!.*?!!\}/s',
            '/<template\b[^>]*>/s',
        ];

        return preg_replace($patterns, ' ', $source);
    }

    private function allowed(string $value): bool
    {
        return in_array(trim($value), self::ALLOWED, true);
    }
}
