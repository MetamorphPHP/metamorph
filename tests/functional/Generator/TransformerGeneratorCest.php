<?php
declare(strict_types=1);

namespace Tests\Functional\Generator;

use FunctionalTester;
use Metamorph\Context\TransformerType;
use Metamorph\Generator\TransformerGenerator;

class TransformerGeneratorCest
{
    public function __construct()
    {
        $this->config = [
            'objects' => [
                'user' => [
                    'class' => TestUser::class,
                    'path' => __DIR__ . '../../_support/Fixture/Transformer',
                    'namespace' => 'Tests\Fixture\Transformer',
                    'properties' => [
                        'id' => [
                            'type' => 'uuid',
                        ],
                    ],
                ],
            ],
            'transformers' => [
                'mongo' => [
                    'class' => null,
                    'properties' => [
                        'id' => [
                            'name' => '_id',
                            'transformers' => [
                                'object' => 'new Binary($objectId->getBytes(), Binary::TYPE_UUID)',
                            ],
                        ],
                    ]
                ],
                '_usage' => [
                    'user' => [
                        'request' => 'object',
                        'object' => 'mongo',
                        'mongo' => [
                            'response',
                            'mats',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function testClassGeneration(FunctionalTester $I)
    {
        $generator = new TransformerGenerator($this->config);

        $transformerType = (new TransformerType())
            ->setFrom('object')
            ->setTo('mongo')
            ->setType('user');

        $generator->generateType($transformerType);


    }

    private function expectedClass()
    {
        return <<<'CLASS'
<?php

namespace Tests\Fixture\Transformer;

class UserObjectToMongoTransformer extends AbstractTransformer
{
    public function transform(ResourceInterface $resource)
    {
        $userObject = $this->getPropertyValueFromResource($resource);

        $user = [];
        $objectId = $userObject->getId();
        $user['_id'] = new Binary($objectId->getBytes(), Binary::TYPE_UUID);
        
        return $user;
    }
}

CLASS;
    }
}
