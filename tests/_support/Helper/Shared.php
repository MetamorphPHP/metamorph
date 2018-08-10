<?php
declare(strict_types=1);

namespace Helper;

abstract class Shared extends \Codeception\Module
{
    public function clearTransformerDirectory()
    {
        $path = realpath(__DIR__ . '/../Fixture/Transformer') . '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if (in_array($file, ['.gitignore', '..', '.'])) {
                continue;
            }
            unlink($path . $file);
        }
    }

    public function expectedUserArrayToObjectTransformer(): string
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
        $userObject->setUsername($userArray['username']);
        return $userObject;
    }
    public function setExclusions(AbstractResource $resource)
    {
    }
}
CLASS;
    }

    public function expectedUserObjectToArrayTransformer(): string
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
        $userAddressObject = $userObject->getAddress();
        $userAddressArray = [];
        $userAddressArray['city'] = $userAddressObject->getCity();
        $userAddressArray['state'] = $userAddressObject->getState();
        foreach ($this->excludedAddressProperties as $propertyToUnset) {
            unset($userAddressArray[$propertyToUnset]);
        }
        $userEmailObjectCollection = $userObject->getEmail();
        $userEmailArrayCollection = [];
        foreach ($userEmailObjectCollection as $userEmailObject) {
            $userEmailArray = [];
            $userEmailArray['label'] = $userEmailObject->getLabel();
            $userEmailArray['value'] = $userEmailObject->getValue();
            foreach ($this->excludedEmailProperties as $propertyToUnset) {
                unset($userEmailArray[$propertyToUnset]);
            }
            $userEmailArrayCollection[] = $userEmailArray;
        }
        $userArray = [];
        $userBirthdayObject = $userObject->birthday;
        $userBirth_dayArray = $userBirthdayObject->toIso8601String();
        $userFavoriteNumbersObjectCollection = $userObject->getFavoriteNumbers();
        $userFavoriteNumbersArrayCollection = [];
        foreach ($userFavoriteNumbersObjectCollection as $userFavoriteNumbersObject) {
            $userFavoriteNumbersArray = (string) $userFavoriteNumbersObject;
            $userFavoriteNumbersArrayCollection[] = $userFavoriteNumbersArray;
        }
        $userIdObject = $userObject->getId();
        $user_idArray = $userIdObject->toString();
        $userArray['address'] = $userAddressArray;
        $userArray['allowed'] = $userObject->isAllowed();
        $userArray['birth_day'] = $userBirth_dayArray;
        $userArray['email'] = $userEmailArrayCollection;
        $userArray['favoriteNumbers'] = $userFavoriteNumbersArrayCollection;
        $userArray['_id'] = $user_idArray;
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