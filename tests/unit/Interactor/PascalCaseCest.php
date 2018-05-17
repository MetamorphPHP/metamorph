<?php
declare(strict_types=1);

namespace Tests\Unit\Interactor;

use Metamorph\Interactor\PascalCase;
use UnitTester;

class PascalCaseCest
{
    public function testSnakeCase(UnitTester $I)
    {
        $I->assertSame('SnakeCase', (new PascalCase)('snake_case'));
    }

    public function testDashCase(UnitTester $I)
    {
        $I->assertSame('DashCase', (new PascalCase)('dash-case'));
    }

    public function testCamelCase(UnitTester $I)
    {
        $I->assertSame('CamelCase', (new PascalCase)('camelCase'));
    }
}
