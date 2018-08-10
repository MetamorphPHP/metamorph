<?php
declare(strict_types=1);

namespace Tests\Unit\Transformation;

use Metamorph\Transformation\IntToString;
use UnitTester;

class IntToStringCest
{
    public function testTransform(UnitTester $I)
    {
        $value = (new IntToString)->transform(42);

        $I->assertSame('42', $value);
    }
}