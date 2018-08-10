<?php
declare(strict_types=1);

namespace Tests\Unit\Factory;

use Metamorph\Context\TransformerType;
use Metamorph\Context\UsageTypeContext;
use Metamorph\Factory\UsageTypeContextFactory;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use Tests\Fixture\TestAddress;
use Tests\Fixture\TestConfigNormalized;
use Tests\Fixture\TestEmail;
use Tests\Fixture\TestUser;
use UnitTester;

class UsageTypeContextFactoryCest
{
    private $usageContextFactory;

    public function __construct()
    {
        $this->usageContextFactory = new UsageTypeContextFactory(TestConfigNormalized::get());
    }

    public function testCreateObjectFrom(UnitTester $I)
    {
        $type = (new TransformerType())
            ->setFrom('object')
            ->setType('user');
        $context = $this->usageContextFactory->createFrom($type);

        $I->assertEquals($this->expectedObjectContext(), $context);
    }

    public function testCreateArrayTo(UnitTester $I)
    {
        $type = (new TransformerType())
            ->setTo('array')
            ->setType('user');
        $context = $this->usageContextFactory->createTo($type);
        $I->assertEquals($this->expectedArrayContext(), $context);
    }

    private function expectedObjectContext(): UsageTypeContext
    {
        $getters = [
            'address'         => new MethodCall(new Variable('userObject'), new Identifier('getAddress')),
            'allowed'         => new MethodCall(new Variable('userObject'), new Identifier('isAllowed')),
            'birthday'        => new PropertyFetch(new Variable('userObject'), new Identifier('birthday')),
            'email'           => new MethodCall(new Variable('userObject'), new Identifier('getEmail')),
            'favoriteNumbers' => new MethodCall(new Variable('userObject'), new Identifier('getFavoriteNumbers')),
            'id'              => new MethodCall(new Variable('userObject'), new Identifier('getId')),
            'username'        => new MethodCall(new Variable('userObject'), new Identifier('getUsername')),
        ];
        $properties = [
            'address'         => 'address',
            'allowed'         => 'allowed',
            'birthday'        => 'birthday',
            'email'           => 'email',
            'favoriteNumbers' => 'favoriteNumbers',
            'id'              => 'id',
            'username'        => 'username',
        ];
        $setters = [
            'address'         => [new Variable('userObject'), new Identifier('setAddress')],
            'allowed'         => [new Variable('userObject'), new Identifier('setAllowed')],
            'birthday'        => new PropertyFetch(new Variable('userObject'), new Identifier('birthday')),
            'email'           => [new Variable('userObject'), new Identifier('setEmail')],
            'favoriteNumbers' => [new Variable('userObject'), new Identifier('setFavoriteNumbers')],
            'id'              => [new Variable('userObject'), new Identifier('setId')],
            'username'        => [new Variable('userObject'), new Identifier('setUsername')],
        ];
        $types = [
            'address'         => [
                'isCollection' => false,
                'object'       => 'address',
            ],
            'allowed'         => [
                'isCollection' => false,
                'scalar'       => 'bool',
            ],
            'birthday'        => [
                'class'        => 'Carbon\Carbon',
                'isCollection' => false,
            ],
            'email'           => [
                'isCollection' => true,
                'object'       => 'email',
            ],
            'favoriteNumbers' => [
                'isCollection' => true,
                'scalar'       => 'int',
            ],
            'id'              => [
                'class'        => 'Ramsey\Uuid',
                'isCollection' => false,
            ],
            'username'        => [
                'isCollection' => false,
                'scalar'       => 'string',
            ],
        ];

        return (new UsageTypeContext())
            ->setClass(TestUser::class)
            ->setGetters($getters)
            ->setName('user')
            ->setNamespace('Tests\Fixture\Transformer')
            ->setObjects($this->expectedObjectObjects())
            ->setPath(realpath(__DIR__.'/../../_support/Fixture/Transformer'))
            ->setProperties($properties)
            ->setTypes($types)
            ->setSetters($setters)
            ->setUsage('object');
    }

    private function expectedObjectObjects(): array
    {
        $addressGetters = [
            'city'  => new MethodCall(new Variable('userAddressObject'), new Identifier('getCity')),
            'state' => new MethodCall(new Variable('userAddressObject'), new Identifier('getState')),
            'zip'   => new MethodCall(new Variable('userAddressObject'), new Identifier('getZip')),
        ];

        $addressProperties = [
            'city'  => 'city',
            'state' => 'state',
            'zip'   => 'zip',
        ];

        $addressSetters = [
            'city'  => [new Variable('userAddressObject'), new Identifier('setCity')],
            'state' => [new Variable('userAddressObject'), new Identifier('setState')],
            'zip'   => [new Variable('userAddressObject'), new Identifier('setZip')],
        ];
        $addressTypes = [
            'city'  => [
                'isCollection' => false,
                'scalar'       => 'string',
            ],
            'state' => [
                'isCollection' => false,
                'scalar'       => 'string',
            ],
            'zip'   => [
                'isCollection' => false,
                'scalar'       => 'string',
            ],
        ];
        $emailGetters = [
            'label'  => new MethodCall(new Variable('userEmailObject'), new Identifier('getLabel')),
            'value' => new MethodCall(new Variable('userEmailObject'), new Identifier('getValue')),
        ];

        $emailProperties = [
            'label'  => 'label',
            'value' => 'value',
        ];

        $emailSetters = [
            'label'  => [new Variable('userEmailObject'), new Identifier('setLabel')],
            'value' => [new Variable('userEmailObject'), new Identifier('setValue')],
        ];
        $emailTypes = [
            'label'  => [
                'isCollection' => false,
                'scalar'       => 'string',
            ],
            'value' => [
                'isCollection' => false,
                'scalar'       => 'string',
            ],
        ];

        return [
            'address' => (new UsageTypeContext())
                ->setClass(TestAddress::class)
                ->setGetters($addressGetters)
                ->setName('address')
                ->setNamespace('Tests\Fixture\Transformer\User')
                ->setObjects([])
                ->setPath(realpath(__DIR__.'/../../_support/Fixture/Transformer').'/User')
                ->setProperties($addressProperties)
                ->setSetters($addressSetters)
                ->setTypes($addressTypes)
                ->setUsage('object'),
            'email' => (new UsageTypeContext())
                ->setClass(TestEmail::class)
                ->setGetters($emailGetters)
                ->setName('email')
                ->setNamespace('Tests\Fixture\Transformer')
                ->setObjects([])
                ->setPath(realpath(__DIR__.'/../../_support/Fixture/Transformer'))
                ->setProperties($emailProperties)
                ->setSetters($emailSetters)
                ->setTypes($emailTypes)
                ->setUsage('object'),
        ];
    }

    private function expectedArrayContext(): UsageTypeContext
    {
        $getters = [
            'address'         => new ArrayDimFetch(new Variable('userArray'), new String_('address')),
            'allowed'         => new ArrayDimFetch(new Variable('userArray'), new String_('allowed')),
            'birthday'        => new ArrayDimFetch(new Variable('userArray'), new String_('birth_day')),
            'email'           => new ArrayDimFetch(new Variable('userArray'), new String_('email')),
            'favoriteNumbers' => new ArrayDimFetch(new Variable('userArray'), new String_('favoriteNumbers')),
            'id'              => new ArrayDimFetch(new Variable('userArray'), new String_('_id')),
            'username'        => new ArrayDimFetch(new Variable('userArray'), new String_('username')),
        ];
        $properties = [
            'address'         => 'address',
            'allowed'         => 'allowed',
            'birthday'        => 'birth_day',
            'email'           => 'email',
            'favoriteNumbers' => 'favoriteNumbers',
            'id'              => '_id',
            'username'        => 'username',
        ];
        $setters = [
            'address'         => new ArrayDimFetch(new Variable('userArray'), new String_('address')),
            'allowed'         => new ArrayDimFetch(new Variable('userArray'), new String_('allowed')),
            'birthday'        => new ArrayDimFetch(new Variable('userArray'), new String_('birth_day')),
            'email'           => new ArrayDimFetch(new Variable('userArray'), new String_('email')),
            'favoriteNumbers' => new ArrayDimFetch(new Variable('userArray'), new String_('favoriteNumbers')),
            'id'              => new ArrayDimFetch(new Variable('userArray'), new String_('_id')),
            'username'        => new ArrayDimFetch(new Variable('userArray'), new String_('username')),
        ];
        $types = [
            'address'         => [
                'isCollection' => false,
                'object'       => 'address',
            ],
            'allowed'         => [
                'isCollection' => false,
                'scalar'       => 'bool',
            ],
            'birthday'        => [
                'isCollection' => false,
                '_from'        => [
                    'format' => 'inclusiveDateTime',
                ],
                '_to'          => [
                    'format' => 'ISO8601',
                ],
            ],
            'email'           => [
                'isCollection' => true,
                'object'       => 'email',
            ],
            'favoriteNumbers' => [
                'isCollection' => true,
                'scalar'       => 'string',
            ],
            'id'              => [
                'isCollection' => false,
                'scalar'       => 'string',
            ],
            'username'        => [
                'isCollection' => false,
                'scalar'       => 'string',
            ],
        ];

        return (new UsageTypeContext())
            ->setClass(null)
            ->setGetters($getters)
            ->setName('user')
            ->setNamespace('Tests\Fixture\Transformer')
            ->setObjects($this->expectedArrayObjects())
            ->setPath(realpath(__DIR__.'/../../_support/Fixture/Transformer'))
            ->setProperties($properties)
            ->setSetters($setters)
            ->setTypes($types)
            ->setUsage('array');
    }

    private function expectedArrayObjects(): array
    {
        $addressGetters = [
            'city'  => new ArrayDimFetch(new Variable('userAddressArray'), new String_('city')),
            'state' => new ArrayDimFetch(new Variable('userAddressArray'), new String_('state')),
        ];
        $addressProperties = [
            'city'  => 'city',
            'state' => 'state',
        ];
        $addressSetters = [
            'city'  => new ArrayDimFetch(new Variable('userAddressArray'), new String_('city')),
            'state' => new ArrayDimFetch(new Variable('userAddressArray'), new String_('state')),
        ];
        $addressTypes = [
            'city'  => [
                'isCollection' => false,
                'scalar'       => 'string',
            ],
            'state' => [
                'isCollection' => false,
                'scalar'       => 'string',
            ],
        ];
        $emailGetters = [
            'label' => new ArrayDimFetch(new Variable('userEmailArray'), new String_('label')),
            'value' => new ArrayDimFetch(new Variable('userEmailArray'), new String_('value')),
        ];
        $emailProperties = [
            'label' => 'label',
            'value' => 'value',
        ];
        $emailSetters = [
            'label' => new ArrayDimFetch(new Variable('userEmailArray'), new String_('label')),
            'value' => new ArrayDimFetch(new Variable('userEmailArray'), new String_('value')),
        ];
        $emailTypes = [
            'label'  => [
                'isCollection' => false,
                'scalar'       => 'string',
            ],
            'value' => [
                'isCollection' => false,
                'scalar'       => 'string',
            ],
        ];

        return [
            'address' => (new UsageTypeContext())
                ->setClass(null)
                ->setGetters($addressGetters)
                ->setName('address')
                ->setNamespace('Tests\Fixture\Transformer\User')
                ->setObjects([])
                ->setPath(realpath(__DIR__.'/../../_support/Fixture/Transformer').'/User')
                ->setProperties($addressProperties)
                ->setSetters($addressSetters)
                ->setTypes($addressTypes)
                ->setUsage('array'),
            'email'   => (new UsageTypeContext())
                ->setClass(null)
                ->setGetters($emailGetters)
                ->setName('email')
                ->setNamespace('Tests\Fixture\Transformer')
                ->setObjects([])
                ->setPath(realpath(__DIR__.'/../../_support/Fixture/Transformer'))
                ->setProperties($emailProperties)
                ->setSetters($emailSetters)
                ->setTypes($emailTypes)
                ->setUsage('array'),

        ];
    }
}
