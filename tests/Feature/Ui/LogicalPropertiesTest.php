<?php

namespace Tests\Feature\Ui;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * RTL-readiness lint: layout must use logical utilities (ms-/me-/ps-/pe-/
 * start-/end-/text-start...) so the same markup works in Arabic.
 */
class LogicalPropertiesTest extends TestCase
{
    private const PATTERNS = [
        // ml-4, -mr-2, md:pl-6, left-0, right-[3px], inset-l-..., border-l-2, rounded-tl-lg, scroll-mr-...
        '/(?<![\w-])(?:[a-z0-9-]+:)*-?(?:m[lr]|p[lr]|left|right|inset-[lr]|border-[lr]|rounded-[lr]|rounded-[tb][lr]|scroll-[mp][lr])-[\w\[]/',
        // text-left, float-right, border-l, rounded-r
        '/(?<![\w-])(?:[a-z0-9-]+:)*(?:(?:text|float|clear)-(?:left|right)|(?:border|rounded)-[lr])(?![\w-])/',
    ];

    public function test_views_and_app_code_use_logical_direction_utilities_only(): void
    {
        $violations = [];

        foreach ([resource_path('views'), app_path()] as $directory) {
            foreach (File::allFiles($directory) as $file) {
                foreach (file($file->getPathname()) as $number => $line) {
                    foreach (self::PATTERNS as $pattern) {
                        if (preg_match($pattern, $line, $match)) {
                            $violations[] = sprintf('%s:%d  %s', $file->getRelativePathname(), $number + 1, $match[0]);
                        }
                    }
                }
            }
        }

        $this->assertSame([], $violations, "Use logical utilities (ms-/me-/ps-/pe-/start-/end-/text-start/text-end):\n".implode("\n", $violations));
    }
}
