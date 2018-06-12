<?php
declare(strict_types=1);

namespace Tests\Unit\Transformation;

use UnitTester;

class StringToIntCest
{
    public function testTransform(UnitTester $I)
    {
        $value = (new StringToInt)->transform('42');

        $I->assertSame(42, $value);
    }
}