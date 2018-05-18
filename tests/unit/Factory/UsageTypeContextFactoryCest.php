<?php
declare(strict_types=1);

namespace Tests\Unit\Factory;

use Metamorph\Context\UsageTypeContext;
use Metamorph\Factory\UsageTypeContextFactory;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
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
        $context = $this->usageContextFactory->create('object', 'user');

        $I->assertEquals($this->expectedObjectContext(), $context);
    }

    public function testCreateArray(UnitTester $I)
    {
        $context = $this->usageContextFactory->create('array', 'user');

        $I->assertEquals($this->expectedArrayContext(), $context); 
    }
    
    private function config(): array
    {
        return [
            'objects' => [
                'user' => [
                    'class'      => TestUser::class,
                    'path'       => __DIR__.'/../../_support/Fixture/Transformer',
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
            'transformers' => [
                'array' => [
                    'user' => [
                        'class' => null,
                        'properties' => [
                            'birthday' => [
                                'type' => 'ISO8601',
                                'name' => 'birth_day',
                            ],
                            'id' => [
                                'type' => 'string',
                            ]
                        ],
                    ],
                ]
            ],
        ];
    }

    private function expectedObjectContext(): UsageTypeContext
    {
        $getters = [
            'allowed'   => new MethodCall(new Variable('user'), new Identifier('isAllowed')),
            'birthday'   => new PropertyFetch(new Variable('user'), new Identifier('birthday')),
            'id'   => new MethodCall(new Variable('user'), new Identifier('getId')),
            'qualified'   => new MethodCall(new Variable('user'), new Identifier('getQualified')),
            'username'   => new MethodCall(new Variable('user'), new Identifier('getUsername')),
        ];
        $properties = [
            'allowed'   => 'allowed',
            'birthday'  => 'birthday',
            'id'        => 'id',
            'qualified' => 'qualified',
            'username'  => 'username',
        ];
        $setters = [
            'allowed'   => [new Variable('user'), new Identifier('setAllowed')],
            'birthday'   => new PropertyFetch(new Variable('user'), new Identifier('birthday')),
            'id'   => [new Variable('user'), new Identifier('setId')],
            'qualified'   => [new Variable('user'), new Identifier('setQualified')],
            'username'   => [new Variable('user'), new Identifier('setUsername')],
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
    
    private function expectedArrayContext(): UsageTypeContext
    {
        $getters = [
            'allowed'   => new ArrayDimFetch(new Variable('user'), new String_('allowed')),
            'birthday'   => new ArrayDimFetch(new Variable('user'), new String_('birth_day')),
            'id'   => new ArrayDimFetch(new Variable('user'), new String_('id')),
            'qualified'   => new ArrayDimFetch(new Variable('user'), new String_('qualified')),
            'username'   => new ArrayDimFetch(new Variable('user'), new String_('username')),
        ];
        $properties = [
            'allowed'   => 'allowed',
            'birthday'  => 'birth_day',
            'id'        => 'id',
            'qualified' => 'qualified',
            'username'  => 'username',
        ];
        $setters = [
            'allowed'   => new ArrayDimFetch(new Variable('user'), new String_('allowed')),
            'birthday'   => new ArrayDimFetch(new Variable('user'), new String_('birth_day')),
            'id'   => new ArrayDimFetch(new Variable('user'), new String_('id')),
            'qualified'   => new ArrayDimFetch(new Variable('user'), new String_('qualified')),
            'username'   => new ArrayDimFetch(new Variable('user'), new String_('username')),
        ];

        return (new UsageTypeContext())
            ->setClass(null)
            ->setGetters($getters)
            ->setName('user')
            ->setNamespace('Tests\Fixture\Transformer')
            ->setPath(__DIR__.'/../../_support/Fixture/Transformer')
            ->setProperties($properties)
            ->setSetters($setters)
            ->setUsage('array');
    }
}
