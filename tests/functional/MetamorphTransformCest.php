<?php
declare(strict_types=1);

namespace Tests\Functional;

use Carbon\Carbon;
use Faker\Factory as Faker;
use FunctionalTester;
use Metamorph\Context\TransformerType;
use Metamorph\Generator\TransformerGenerator;
use Metamorph\Metamorph;
use Metamorph\Resource\Collection;
use Ramsey\Uuid\Uuid;
use Tests\Fixture\TestAddress;
use Tests\Fixture\TestConfigNormalized;
use Tests\Fixture\TestEmail;
use Tests\Fixture\TestUser;

class MetamorphTransformCest
{
    private $collectionData = [];
    private $expectedData = [];

    public function __construct()
    {
        $faker = Faker::create();

        $x = 0;
        do {
            $totalEmails = rand(1, 3);
            $emailCount = 0;
            $emails = [];
            do {
                $emails[] = [
                    'label' => $faker->randomElement(['home', 'work', 'secret']),
                    'value' => $faker->email,
                ];
                $emailCount++;
            } while ($emailCount < $totalEmails);

            $totalNumbers = rand(2, 4);
            $numberCount = 0;
            $favoriteNumbers = [];
            do {
                $favoriteNumbers[] = (string) rand(0, 100);
                $numberCount++;
            } while ($numberCount < $totalNumbers);

            $this->collectionData[] = [
                'address' => [
                    'city' => $faker->city,
                    'state' => $faker->state,
                ],
                'allowed' => $faker->boolean,
                'birth_day' => $faker->iso8601,
                'email' => $emails,
                'favoriteNumbers' => $favoriteNumbers,
                '_id' => $faker->uuid,
                'username' => $faker->userName,
            ];
            $x++;
        } while  ($x < 2);

        foreach ($this->collectionData as $datum) {
            $address = (new TestAddress())
                ->setCity($datum['address']['city'])
                ->setState($datum['address']['state']);

            $emails = [];
            foreach ($datum['email'] as $emailData) {
                $emails[] = (new TestEmail())
                    ->setLabel($emailData['label'])
                    ->setValue($emailData['value']);
            }
            $favoriteNumbers = [];
            foreach ($datum['favoriteNumbers'] as $number) {
                $favoriteNumbers[] = (int) $number;
            }
            $user = (new TestUser())
                ->setAddress($address)
                ->setAllowed($datum['allowed'])
                ->setEmail($emails)
                ->setFavoriteNumbers($favoriteNumbers)
                ->setId(Uuid::fromString($datum['_id']))
                ->setUsername($datum['username']);
            $user->birthday = new Carbon($datum['birth_day']);

            $this->expectedData[] = $user;
        }

        $generator = new TransformerGenerator(TestConfigNormalized::get());

        $transformerType = (new TransformerType())
            ->setFrom('array')
            ->setTo('object')
            ->setType('user');

        $generator->generateType($transformerType);
    }

    public function testTransformCollection(FunctionalTester $I)
    {
        $config = TestConfigNormalized::get();
        $transformer = new Metamorph($config);

        $resource = new Collection($this->collectionData);

        $transformedData = $transformer->transform($resource)->as('user')->from('array')->to('object');

        $I->assertEquals($this->expectedData, $transformedData);
    }
}