<?php
declare(strict_types=1);

namespace Tests\Functional\Generator;

use FunctionalTester;
use Metamorph\Context\TransformerType;
use Metamorph\Generator\TransformerGenerator;
use PhpParser\ParserFactory;
use Tests\Fixture\TestConfigNormalized;

class TransformerGeneratorFromArrayToObjectCest
{
    private $unset = ['something'];

    public function __construct()
    {
    }

    public function testClassGeneration(FunctionalTester $I)
    {
        $generator = new TransformerGenerator(TestConfigNormalized::get());

        $transformerType = (new TransformerType())
            ->setFrom('array')
            ->setTo('object')
            ->setType('user');

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse($this->expectedClass());

        $generator->generateType($transformerType);
    }

    private function expectedClass()
    {
        return <<<'CLASS'
<?php

namespace Tests\Fixture\Transformer;

use Metamorph\Metamorph\AbstractResource;
use Metamorph\TransformerInterface;
class UserArrayToObjectTransformer implements TransformerInterface
{
    public function transform(AbstractResource $resource)
    {
        $userArray = $resource->getValue();
        $addressArray = $userArray['address'];
        $addressObject = new \Tests\Functional\TestAddress();
        $addressObject->setCity($addressArray['city']);
        $addressObject->setState($addressArray['state']);
        
        $userObjectId = Uuid::uuid4($userArray['_id']);
        
        $userObjectBirthday = $userArray->getBirthday()->toIso8601String();
        $userObject = new \Tests\Functional\TestUser();
        $userObject->setAddress($addressObject);
        $userObject->setAllowed($userArray['allowed']);
        $userObject->birthday = $userObjectBirthday;
        $userObject->setId($userObjectId);
        $userObject->setQualified($userArray['qualified']);
        $userObject->setUsername($userArray['username']);
        return $userObject;
    }
    
    public function setExclusions(AbstractResource $resource)
    {
    }
}

CLASS;
    }
}
