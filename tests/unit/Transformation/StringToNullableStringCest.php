<?php
declare(strict_types=1);

namespace Tests\Unit\Transformation;

use Metamorph\Transformation\StringToNullableString;
use UnitTester;

class StringToNullableStringCest
{
    public function testTransformWithNull(UnitTester $I)
    {
        $I->assertNull((new StringToNullableString)->transform(null));
    }

    public function testTransformWithValue(UnitTester $I)
    {
        $value = 'value';

        $I->assertSame($value, (new StringToNullableString)->transform($value));
    }
}
