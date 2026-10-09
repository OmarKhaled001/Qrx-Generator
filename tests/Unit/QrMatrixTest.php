<?php

namespace Tests\Unit;

use App\Support\QrMatrix;
use PHPUnit\Framework\TestCase;

class QrMatrixTest extends TestCase
{
    public function test_it_returns_the_modules_and_outward_particles(): void
    {
        $qr = QrMatrix::for('https://example.com', particles: 10);

        $this->assertSame(0, ($qr['size'] - 21) % 4); // QR versions grow 4 modules at a time
        $this->assertGreaterThan(100, substr_count($qr['path'], 'h1v1h-1z'));
        $this->assertCount(10, $qr['particles']);

        $center = ($qr['size'] - 1) / 2;
        foreach ($qr['particles'] as $particle) {
            $this->assertSame($particle['x'] <=> $center, $particle['dx'] <=> 0); // drifts away from the centre
        }
    }
}
