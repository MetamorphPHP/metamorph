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

        $fileName = __DIR__ . '/../../_support/Fixture/Transformer/UserArrayToObjectTransformer.php';
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
class UserArrayToObjectTransformer implements TransformerInterface
{
    public function transform(AbstractResource $resource)
    {
        $userArray = $resource->getValue();
        $userAddressArray = $userArray['address'];
        $userAddressObject = new \Tests\Fixture\TestAddress();
        $userAddressObject->setCity($userAddressArray['city']);
        $userAddressObject->setState($userAddressArray['state']);
        $userEmailArrayCollection = $userArray['email'];
        $userEmailObjectCollection = [];
        foreach ($userEmailArrayCollection as $userEmailArray) {
            $userEmailObject = new \Tests\Fixture\TestEmail();
            $userEmailObject->setLabel($userEmailArray['label']);
            $userEmailObject->setValue($userEmailArray['value']);
            $userEmailObjectCollection[] = $userEmailObject;
        }
        $userObject = new \Tests\Fixture\TestUser();
        $userBirth_dayArray = $userArray['birth_day'];
        if (empty($userBirth_dayArray)) {
            $userBirthdayObject = null;
        }
        try {
            if ($userBirth_dayArray instanceof \MongoDB\BSON\UTCDateTime) {
                $userBirth_dayArray = $userBirth_dayArray->toDateTime();
            }
            if ($userBirth_dayArray instanceof \Carbon\Carbon) {
                $userBirthdayObject = $userBirth_dayArray;
            }
            if ($userBirth_dayArray instanceof \DateTime) {
                $userBirthdayObjectCarbon = new \Carbon\Carbon($userBirth_dayArray);
                $userBirthdayObject = $userBirthdayObjectCarbon;
            }
            if (is_string($userBirth_dayArray)) {
                $userBirthdayObjectCarbon = new \Carbon\Carbon($userBirth_dayArray);
                $userBirthdayObject = $userBirthdayObjectCarbon;
            }
        } catch (\Exception $userBirthdayObjectE) {
            throw new \Metamorph\Exception\TransformException('Failed to transform userArrayBirthday because Carbon.');
        }
        $userFavoriteNumbersArrayCollection = $userArray['favoriteNumbers'];
        $userFavoriteNumbersObjectCollection = [];
        foreach ($userFavoriteNumbersArrayCollection as $userFavoriteNumbersArray) {
            $userFavoriteNumbersObject = (int) $userFavoriteNumbersArray;
            $userFavoriteNumbersObjectCollection[] = $userFavoriteNumbersObject;
        }
        $user_idArray = $userArray['_id'];
        $userIdObject = \Ramsey\Uuid\Uuid::fromString($user_idArray);
        $userObject->setAddress($userAddressObject);
        $userObject->setAllowed($userArray['allowed']);
        $userObject->birthday = $userBirthdayObject;
        $userObject->setEmail($userEmailObjectCollection);
        $userObject->setFavoriteNumbers($userFavoriteNumbersObjectCollection);
        $userObject->setId($userIdObject);
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
