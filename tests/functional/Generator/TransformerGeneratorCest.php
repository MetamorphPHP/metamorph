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
use Tests\Fixture\TestConfigNormalized;

class TransformerGeneratorCest
{
    private $unset = ['something'];

    public function __construct()
    {
    }

    public function testClassGeneration(FunctionalTester $I)
    {
        $generator = new TransformerGenerator(TestConfigNormalized::get());

        $transformerType = (new TransformerType())
            ->setFrom('object')
            ->setTo('array')
            ->setType('user');

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse($this->expectedClass());

        $generator->generateType($transformerType);

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

use Metamorph\Metamorph\AbstractResource;
use Metamorph\TransformerInterface;

class UserObjectToArrayTransformer implements TransformerInterface
{
    private $excludedAddressProperties = [];
    private $excludedUserProperties = [];
    
    public function transform(AbstractResource $resource)
    {
        $userObject = $resource->getValue();
        
        $addressArray = [];
        $addressArray['city'] = $addressObject->getCity();
        $addressArray['state'] = $addressObject->getState();
        foreach ($this->excludedAddressProperties as $propertyToUnset) {
            unset($address[$propertyToUnset]);
        }
        $userArrayId = $userObject->getId()->toString();
        $userArrayBirthday = $userObject->getBirthday()->toIso8601String();
        $userArray = [];
        $userArray['address'] = $addressArray;
        $userArray['allowed'] = $userObject->getAllowed();
        $userArray['birth_day'] = $userArrayBirthday;
        $userArray['_id'] = $userArrayId;
        $userArray['qualified'] = $userObject->getQualified();
        $userArray['username'] = $userObject->getUsername();
        
        foreach ($this->excludedUserProperties as $propertyToUnset) {
            unset($user[$propertyToUnset]);
        }
        
        return $userArray;
    }
    
    public function setExclusions(AbstractResource $resource)
    {
        $this->excludedAddressProperties = $resource->getExcludedProperties('address');
        $this->excludedUserProperties = $resource->getExcludedProperties('user');
    }
}

CLASS;
    }
}
