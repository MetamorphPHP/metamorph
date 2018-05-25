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
        $userArrayId = $userArray['_id'];
        $userObjectId = Uuid::uuid4($userArrayId);
        $userArrayBirthday = $userArray['birth_day'];
        if (empty($userArrayBirthday)) {
            $userObjectBirthday = null;
        }
        try {
            if ($userArrayBirthday instanceof UTCDateTime) {
                $userArrayBirthday = $userArrayBirthday->toDateTime();
            }
            if ($userArrayBirthday instanceof Carbon) {
                $userObjectBirthday = $userArrayBirthday;
            }
            if ($userArrayBirthday instanceof DateTime) {
                $userObjectBirthdayCarbon = new Carbon($userArrayBirthday);
                $userObjectBirthday = $userObjectBirthdayCarbon;
            }
            if (is_string($userArrayBirthday)) {
                $userObjectBirthdayCarbon = new Carbon($userArrayBirthday);
                $userObjectBirthday = $userObjectBirthdayCarbon;
            }
            $userObjectBirthday = null;
        } catch (Exception $e) {
            throw new TransformException('Failed to transform userArrayBirthday because Carbon.');
        }
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
