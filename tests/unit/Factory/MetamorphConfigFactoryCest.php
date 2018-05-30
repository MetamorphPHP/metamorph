<?php
declare(strict_types=1);

namespace Tests\Unit\Factory;

use Metamorph\Factory\MetamorphConfigFactory;
use Tests\Fixture\TestConfig;
use Tests\Fixture\TestConfigNormalized;
use UnitTester;

class MetamorphConfigFactoryCest
{
    public function testInvoke(UnitTester $I)
    {
        $config = TestConfig::get();

        $normalized = (new MetamorphConfigFactory())($config);

        $expected = TestConfigNormalized::get();

        $I->assertEquals($expected, $normalized);
    }
}