<?php

namespace Tests\Feature\Ui;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

/**
 * Every __('...') string in views and app code has an Arabic translation, and
 * each translation keeps the same :placeholders.
 */
class TranslationCoverageTest extends TestCase
{
    /**
     * @return list<string>
     */
    public static function translationKeys(): array
    {
        $keys = [];

        foreach ([resource_path('views'), app_path()] as $directory) {
            foreach (File::allFiles($directory) as $file) {
                preg_match_all('/(?:__|@lang|trans)\(\s*([\'"])((?:\\\\.|(?!\1).)+)\1/s', file_get_contents($file->getPathname()), $matches);
                foreach ($matches[2] as $index => $key) {
                    $keys[] = $matches[1][$index] === "'" ? str_replace("\\'", "'", $key) : stripcslashes($key);
                }
            }
        }

        return array_values(array_unique($keys));
    }

    public function test_every_string_has_an_arabic_translation(): void
    {
        $arabic = json_decode(File::get(lang_path('ar.json')), true, flags: JSON_THROW_ON_ERROR);

        $missing = array_values(array_filter(
            self::translationKeys(),
            fn (string $key) => ! array_key_exists($key, $arabic) && ! Lang::hasForLocale($key, 'ar'),
        ));

        $this->assertSame([], $missing, "Missing from lang/ar.json:\n".implode("\n", $missing));
    }

    public function test_translations_keep_their_placeholders(): void
    {
        $arabic = json_decode(File::get(lang_path('ar.json')), true, flags: JSON_THROW_ON_ERROR);
        $broken = [];

        foreach ($arabic as $key => $translation) {
            preg_match_all('/:[a-z_]+/', $key, $expected);
            preg_match_all('/:[a-z_]+/', $translation, $actual);
            sort($expected[0]);
            sort($actual[0]);

            if ($expected[0] !== $actual[0]) {
                $broken[] = $key;
            }
        }

        $this->assertSame([], $broken, "Placeholders differ:\n".implode("\n", $broken));
    }
}
