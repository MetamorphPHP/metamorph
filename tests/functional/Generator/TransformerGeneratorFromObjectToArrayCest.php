<?php
declare(strict_types=1);

namespace Tests\Functional\Generator;

use FunctionalTester;
use Metamorph\Context\TransformerType;
use Metamorph\Generator\TransformerGenerator;
use PhpParser\ParserFactory;
use Tests\Fixture\TestConfigNormalized;

class TransformerGeneratorFromObjectToArrayCest
{
    public function _before(FunctionalTester $I)
    {
        $I->clearGeneratorDirectory();
    }

    public function testClassGeneration(FunctionalTester $I)
    {
        $generator = new TransformerGenerator(TestConfigNormalized::get());

        $transformerType = (new TransformerType())
            ->setFrom('object')
            ->setTo('array')
            ->setType('user');

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse($I->expectedUserObjectToArrayTransformer());

        $generator->generateType($transformerType);

        $fileName = __DIR__ . '/../../_support/Fixture/Transformer/UserObjectToArrayTransformer.php';
        $contents = file_get_contents($fileName);

        $I->assertSame($I->expectedUserObjectToArrayTransformer(), $contents);
    }
}
