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

        $fileName = __DIR__ . '/../../_support/Fixture/Transformer/UserObjectToArrayTransformer.php';
        $contents = file_get_contents($fileName);

        $I->assertSame($this->expectedClass(), $contents);
    }

    private function expectedClass()
    {
        return <<<'CLASS'
<?php

namespace Tests\Fixture\Transformer;

use Metamorph\Resource\AbstractResource;
use Metamorph\TransformerInterface;
class UserObjectToArrayTransformer implements TransformerInterface
{
    private $excludedAddressProperties = [];
    private $excludedEmailProperties = [];
    private $excludedUserProperties = [];
    public function transform(AbstractResource $resource)
    {
        $userObject = $resource->getValue();
        $addressObject = $userObject->getAddress();
        $addressArray = [];
        $addressArray['city'] = $addressObject->getCity();
        $addressArray['state'] = $addressObject->getState();
        foreach ($this->excludedAddressProperties as $propertyToUnset) {
            unset($addressArray[$propertyToUnset]);
        }
        $emailObjectCollection = $userObject->getEmail();
        $emailArrayCollection = [];
        foreach ($emailObjectCollection as $emailObject) {
            $emailArray = [];
            $emailArray['label'] = $emailObject->getLabel();
            $emailArray['value'] = $emailObject->getValue();
            foreach ($this->excludedEmailProperties as $propertyToUnset) {
                unset($emailArray[$propertyToUnset]);
            }
            $emailArrayCollection[] = $emailArray;
        }
        $userArray = [];
        $userObjectId = $userObject->getId();
        $userArrayId = $userObjectId->toString();
        $userArrayBirthday = $userObject->getBirthday()->toIso8601String();
        $userArray['address'] = $addressArray;
        $userArray['allowed'] = $userObject->isAllowed();
        $userArray['birth_day'] = $userArrayBirthday;
        $userArray['email'] = $emailArrayCollection;
        $userArray['_id'] = $userArrayId;
        $userArray['qualified'] = $userObject->getQualified();
        $userArray['username'] = $userObject->getUsername();
        foreach ($this->excludedUserProperties as $propertyToUnset) {
            unset($userArray[$propertyToUnset]);
        }
        return $userArray;
    }
    public function setExclusions(AbstractResource $resource)
    {
        $this->excludedAddressProperties = $resource->getExcludedProperties('address');
        $this->excludedEmailProperties = $resource->getExcludedProperties('email');
        $this->excludedUserProperties = $resource->getExcludedProperties('user');
    }
}
CLASS;
    }
}
