<?php

namespace Tests\Unit;

use App\Support\QrDesign;
use App\Support\QrOptions;
use PHPUnit\Framework\TestCase;

class QrDesignTest extends TestCase
{
    public function test_contrast_ratio_matches_wcag(): void
    {
        $this->assertSame(21.0, QrDesign::contrastRatio('#000000', '#ffffff'));
        $this->assertSame(1.0, QrDesign::contrastRatio('#777777', '#777777'));
        $this->assertSame(QrDesign::contrastRatio('#0e7490', '#ffffff'), QrDesign::contrastRatio('#ffffff', '#0e7490'));
    }

    public function test_weakest_contrast_uses_both_gradient_ends(): void
    {
        $design = ['bg' => '#ffffff', 'gradient' => ['from' => '#000000', 'to' => '#eeeeee']];

        $this->assertLessThan(3, QrDesign::weakestContrast($design));
        $this->assertGreaterThan(15, QrDesign::weakestContrast(['color' => '#000000', 'bg' => '#ffffff']));
    }

    public function test_invalid_colours_fall_back_to_defaults(): void
    {
        $design = QrDesign::normalize(['color' => '#12', 'bg' => 'blue']);

        $this->assertSame(QrDesign::defaults()['color'], $design['color']);
        $this->assertSame(QrDesign::defaults()['bg'], $design['bg']);
        $this->assertFalse(QrDesign::isHex('#GGGGGG'));
        $this->assertTrue(QrDesign::isHex('#A1b2C3'));
    }

    public function test_size_and_margin_are_clamped(): void
    {
        $this->assertSame(100, QrOptions::size(5));
        $this->assertSame(500, QrOptions::size(9999));
        $this->assertSame(2000, QrOptions::size(9999, QrOptions::MAX_DOWNLOAD_SIZE));
        $this->assertSame(0, QrOptions::margin(-3));
        $this->assertSame(10, QrOptions::margin(42));
    }
}
