<?php
declare(strict_types=1);

namespace Tests\Unit\Resource;

use Metamorph\Metamorph;
use Metamorph\Resource\ResourceContext;
use Tests\Fixture\TestObjectToArrayTransformer;
use UnitTester;

class ResourceContextCest
{
    public function testGetTransformer(UnitTester $I)
    {
        $config = [
            'array' => [
                'test' => [
                    'namespace' => 'Tests\Fixture'
                ]
            ]
        ];
        $metamorph = new Metamorph($config);
        $context = (new ResourceContext())
            ->setType('test')
            ->setFrom('object')
            ->setMetamorph($metamorph)
            ->setTo('array');

        $transformer = $context->getTransformer();

        $I->assertInstanceOf(TestObjectToArrayTransformer::class, $transformer);
    }
}