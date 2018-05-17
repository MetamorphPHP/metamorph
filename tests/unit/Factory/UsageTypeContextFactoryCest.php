<?php
declare(strict_types=1);

namespace Tests\Unit\Factory;

use Metamorph\Context\UsageTypeContext;
use Metamorph\Factory\UsageTypeContextFactory;
use Tests\Fixture\TestUser;
use UnitTester;

class UsageTypeContextFactoryCest
{
    private $usageContextFactory;

    public function __construct()
    {
        $this->usageContextFactory = new UsageTypeContextFactory($this->config());
    }

    public function testCreateObject(UnitTester $I)
    {
        $this->usageContextFactory->create('object', 'user');
    }

    private function config(): array
    {
        return [
            'objects' => [
                'user' => [
                    'class'      => TestUser::class,
                    'path'       => __DIR__.'../../_support/Fixture/Transformer',
                    'namespace'  => 'Tests\Fixture\Transformer',
                    'properties' => [
                        'allowed'   => [
                            'type' => 'bool',
                        ],
                        'birthday'  => [
                            'type' => 'Carbon',
                        ],
                        'id'        => [
                            'type' => 'uuid',
                        ],
                        'qualified' => [
                            'type' => 'bool',
                        ],
                        'username'  => [],
                    ],
                ],
            ],
        ];
    }

    private function expectedObjectContext(): UsageTypeContext
    {
        $getters = [
            'allowed'   => '$user->isAllowed()',
            'birthday'  => '$user->birthday',
            'id'        => '$user->getId()',
            'qualified' => '$user->getQualified()',
            'username'  => '$user->getUsername()',
        ];
        $properties = [
            'allowed'   => 'allowed',
            'birthday'  => 'birthday',
            'id'        => 'id',
            'qualified' => 'qualified',
            'username'  => 'username',
        ];
        $setters = [
            'allowed'   => '$user->setAllowed(%_DATA_%)',
            'birthday'  => '$user->birthday = %_DATA_%',
            'id'        => '$user->setId(%_DATA_%)',
            'qualified' => '$user->setQualified(%_DATA_%)',
            'username'  => '$user->setUsername(%_DATA_%)',
        ];

        return (new UsageTypeContext())
            ->setClass(TestUser::class)
            ->setGetters($getters)
            ->setName('user')
            ->setNamespace('Tests\Fixture\Transformer')
            ->setPath(__DIR__.'/../../_support/Fixture/Transformer')
            ->setProperties($properties)
            ->setSetters($setters)
            ->setUsage('object');
    }
}
