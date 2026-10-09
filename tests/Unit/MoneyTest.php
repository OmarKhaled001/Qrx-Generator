<?php

namespace Tests\Unit;

use App\Support\Money;
use Illuminate\Support\Number;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_prices_read_the_same_in_arabic_and_english(): void
    {
        Number::useLocale('ar');

        $this->assertSame('$29', Money::format(2900));
        $this->assertSame('$9.50', Money::format(950));
        $this->assertSame('€12', Money::format(1200, 'eur'));

        Number::useLocale('en');
    }
}
