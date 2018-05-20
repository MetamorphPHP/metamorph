<?php
declare(strict_types=1);

namespace Tests\Functional\Generator;

use FunctionalTester;
use Metamorph\Context\TransformerType;
use Metamorph\Generator\TransformerGenerator;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Tests\Fixture\TestConfig;

class TransformerGeneratorCest
{
    private $unset = ['something'];

    public function __construct()
    {
    }

    public function testClassGeneration(FunctionalTester $I)
    {
        $generator = new TransformerGenerator(TestConfig::get());

        $transformerType = (new TransformerType())
            ->setFrom('object')
            ->setTo('mongo')
            ->setType('user');

     //   $generator->generateType($transformerType);
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse($this->expectedClass());

        $dumper = new NodeDumper();
        $dump = $dumper->dump($ast);

        $factory = new BuilderFactory();
        $assign = new Assign(new Variable('objectId'), new MethodCall());
        $methdod = (new Expression());
        $node = $factory->namespace('Tests\Fixture\Transformer')
            ->addStmt($factory->class('UserObjectToMongoTransformer'))

            ->addStmt($factory->method('transform')
                ->addStmt((new Variable('user'))->addStmt((new Function_('setShit'))->addParam(new Variable('dogfood')))))

            ->getNode();

        $statements = array($node);
        $prettyPrinter = new Standard();
        $results = $prettyPrinter->prettyPrint($statements);
        $a = 0;
    }

    private function expectedClass()
    {
        return <<<'CLASS'
<?php

namespace Tests\Fixture\Transformer;

class UserObjectToArrayTransformer extends AbstractTransformer
{
    public function transform(ResourceInterface $resource)
    {
        $userObject = $this->getPropertyValueFromResource($resource);
        
        $addressArray = [];
        $addressArray['city'] = $addressObject->getCity();
        $addressArray['state'] = $addressObject->getState();
        foreach ($this->excludedAddressProperties as $propertyToUnset) {
            unset($address[$propertyToUnset]);
        }
        $objectId = $userObject->getId();
        $userArray = [];
        $userArray['address'] = $addressArray;
        $userArray['_id'] = $userObject->getId()->;
        
        return $userArray;
    }
}

CLASS;
    }
}
