<?php
declare(strict_types=1);

namespace Tests\Unit\Interactor;

use Metamorph\Interactor\EstablishPath;
use UnitTester;

class EstablishPathCest
{
    CONST TEST_DIRECTORY = __DIR__.'/../../_support/Fixture/Path/To/Test';

    public function _before(UnitTester $I)
    {
        if (is_dir(self::TEST_DIRECTORY)) {
            rmdir(self::TEST_DIRECTORY);
        }
    }

    public function testInvoke(UnitTester $I)
    {
        $results = (new EstablishPath)(self::TEST_DIRECTORY);

        $I->assertTrue($results);
        $I->assertTrue(is_dir(self::TEST_DIRECTORY));

        $results = (new EstablishPath)(self::TEST_DIRECTORY);
        $I->assertTrue($results, 'since the path is there, this should return true');
    }
}
