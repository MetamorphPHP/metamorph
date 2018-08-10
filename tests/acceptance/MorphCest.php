<?php
declare(strict_types=1);

namespace Tests\Acceptance;

use AcceptanceTester;

final class MorphCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->clearTransformerDirectory();
    }

    public function testCommand(AcceptanceTester $I)
    {
        $configPath = realpath(__DIR__ . '/../_support/Fixture/config');
        $morph = realpath(__DIR__ . '/../../morph') . ' generate --path=' . $configPath;
        exec($morph);

        $fileName = realpath(__DIR__ . '/../_support/Fixture/Transformer/UserArrayToObjectTransformer.php');
        $contents = file_get_contents($fileName);

        $I->assertSame($I->expectedUserArrayToObjectTransformer(), $contents);

        $fileName = realpath(__DIR__ . '/../_support/Fixture/Transformer/UserObjectToArrayTransformer.php');
        $contents = file_get_contents($fileName);

        $I->assertSame($I->expectedUserObjectToArrayTransformer(), $contents);
    }
}