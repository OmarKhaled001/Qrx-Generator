<?php

namespace Tests\Unit;

use App\Support\QrDesign;
use App\Support\QrPresets;
use Tests\TestCase;

class QrPresetsTest extends TestCase
{
    public function test_every_preset_is_valid_and_scannable(): void
    {
        foreach (array_keys(QrPresets::all()) as $key) {
            $design = QrPresets::design($key);

            $this->assertSame(array_keys(QrDesign::defaults()), array_keys($design), $key);
            $this->assertGreaterThanOrEqual(4.5, QrDesign::weakestContrast($design), "Preset {$key} needs more contrast to scan reliably");
        }
    }
}
