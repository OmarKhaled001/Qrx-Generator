<?php

namespace Tests\Feature\Ui;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Every translation key exists in every language: the JSON strings (lang/{locale}.json) and
 * each PHP group (lang/{locale}/*.php, nested keys included). Adding a locale to config/qrx.php
 * puts it under the same check.
 */
class TranslationParityTest extends TestCase
{
    /**
     * @return list<string>
     */
    private function locales(): array
    {
        return array_keys(config('qrx.locales'));
    }

    public function test_json_files_have_the_same_keys(): void
    {
        $keys = collect($this->locales())->mapWithKeys(fn (string $locale) => [
            $locale => array_keys(json_decode(File::get(lang_path("{$locale}.json")), true, flags: JSON_THROW_ON_ERROR)),
        ]);

        $this->assertParity($keys->all(), 'lang/:locale.json');
    }

    public function test_php_groups_have_the_same_keys(): void
    {
        $groups = collect($this->locales())
            ->flatMap(fn (string $locale) => array_map(fn (string $path) => basename($path, '.php'), File::glob(lang_path("{$locale}/*.php"))))
            ->unique();

        foreach ($groups as $group) {
            $keys = collect($this->locales())->mapWithKeys(function (string $locale) use ($group) {
                $path = lang_path("{$locale}/{$group}.php");

                return [$locale => File::exists($path) ? array_keys(Arr::dot(require $path)) : []];
            });

            $this->assertParity($keys->all(), "lang/:locale/{$group}.php");
        }
    }

    public function test_every_translation_keeps_the_placeholders_of_its_key(): void
    {
        $broken = [];

        foreach ($this->locales() as $locale) {
            $strings = json_decode(File::get(lang_path("{$locale}.json")), true, flags: JSON_THROW_ON_ERROR);
            foreach ($strings as $key => $value) {
                if ($this->placeholders($key) !== $this->placeholders($value)) {
                    $broken[] = "{$locale}: {$key}";
                }
            }
        }

        $this->assertSame([], $broken, "Placeholders differ from the key:\n".implode("\n", $broken));
    }

    /**
     * @param  array<string, list<string>>  $keysByLocale
     */
    private function assertParity(array $keysByLocale, string $file): void
    {
        $all = array_unique(array_merge(...array_values($keysByLocale)));
        $problems = [];

        foreach ($keysByLocale as $locale => $keys) {
            foreach (array_diff($all, $keys) as $missing) {
                $problems[] = str_replace(':locale', $locale, $file)." is missing: {$missing}";
            }
        }

        $this->assertSame([], $problems, implode("\n", $problems));
    }

    /**
     * @return list<string>
     */
    private function placeholders(string $text): array
    {
        preg_match_all('/:[a-z_]+/', $text, $matches);
        $unique = array_values(array_unique($matches[0]));
        sort($unique);

        return $unique;
    }
}
