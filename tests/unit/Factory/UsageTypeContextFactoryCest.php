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
            'address'   => new MethodCall(new Variable('userObject'), new Identifier('getAddress')),
            'allowed'   => new MethodCall(new Variable('userObject'), new Identifier('isAllowed')),
            'birthday'  => new PropertyFetch(new Variable('userObject'), new Identifier('birthday')),
            'id'        => new MethodCall(new Variable('userObject'), new Identifier('getId')),
            'qualified' => new MethodCall(new Variable('userObject'), new Identifier('getQualified')),
            'username'  => new MethodCall(new Variable('userObject'), new Identifier('getUsername')),
        ];
        $properties = [
            'address'   => 'address',
            'allowed'   => 'allowed',
            'birthday'  => 'birthday',
            'id'        => 'id',
            'qualified' => 'qualified',
            'username'  => 'username',
        ];
        $setters = [
            'address'   => [new Variable('userObject'), new Identifier('setAddress')],
            'allowed'   => [new Variable('userObject'), new Identifier('setAllowed')],
            'birthday'  => new PropertyFetch(new Variable('userObject'), new Identifier('birthday')),
            'id'        => [new Variable('userObject'), new Identifier('setId')],
            'qualified' => [new Variable('userObject'), new Identifier('setQualified')],
            'username'  => [new Variable('userObject'), new Identifier('setUsername')],
        ];

        return (new UsageTypeContext())
            ->setClass(TestUser::class)
            ->setGetters($getters)
            ->setName('user')
            ->setNamespace('Tests\Fixture\Transformer')
            ->setObjects($this->expectedObjectObjects())
            ->setPath(realpath(__DIR__.'/../../_support/Fixture/Transformer'))
            ->setProperties($properties)
            ->setSetters($setters)
            ->setUsage('object');
    }

    private function expectedObjectObjects():array
    {
        $getters = [
            'city'   => new MethodCall(new Variable('addressObject'), new Identifier('getCity')),
            'state'   => new MethodCall(new Variable('addressObject'), new Identifier('getState')),
            'zip'        => new MethodCall(new Variable('addressObject'), new Identifier('getZip')),
        ];

        $properties = [
            'city'   => 'city',
            'state'   => 'state',
            'zip'  => 'zip',
        ];
        $setters = [
            'city'   => [new Variable('addressObject'), new Identifier('setCity')],
            'state'   => [new Variable('addressObject'), new Identifier('setState')],
            'zip'        => [new Variable('addressObject'), new Identifier('setZip')],
        ];

        return [ 'address' => (new UsageTypeContext())
            ->setClass(TestAddress::class)
            ->setGetters($getters)
            ->setName('address')
            ->setNamespace('Tests\Fixture\Transformer\User')
            ->setObjects([])
            ->setPath(realpath(__DIR__.'/../../_support/Fixture/Transformer/User'))
            ->setProperties($properties)
            ->setSetters($setters)
            ->setUsage('object')];
    }

    private function expectedArrayContext(): UsageTypeContext
    {
        $getters = [
            'address'   => new ArrayDimFetch(new Variable('userArray'), new String_('address')),
            'allowed'   => new ArrayDimFetch(new Variable('userArray'), new String_('allowed')),
            'birthday'  => new ArrayDimFetch(new Variable('userArray'), new String_('birth_day')),
            'id'        => new ArrayDimFetch(new Variable('userArray'), new String_('_id')),
            'qualified' => new ArrayDimFetch(new Variable('userArray'), new String_('qualified')),
            'username'  => new ArrayDimFetch(new Variable('userArray'), new String_('username')),
        ];
        $properties = [
            'address'   => 'address',
            'allowed'   => 'allowed',
            'birthday'  => 'birth_day',
            'id'        => '_id',
            'qualified' => 'qualified',
            'username'  => 'username',
        ];
        $setters = [
            'address'   => new ArrayDimFetch(new Variable('userArray'), new String_('address')),
            'allowed'   => new ArrayDimFetch(new Variable('userArray'), new String_('allowed')),
            'birthday'  => new ArrayDimFetch(new Variable('userArray'), new String_('birth_day')),
            'id'        => new ArrayDimFetch(new Variable('userArray'), new String_('_id')),
            'qualified' => new ArrayDimFetch(new Variable('userArray'), new String_('qualified')),
            'username'  => new ArrayDimFetch(new Variable('userArray'), new String_('username')),
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
            ->setUsage('array');
    }

    private function expectedArrayObjects(): array
    {
        $getters = [
            'city'   => new ArrayDimFetch(new Variable('addressArray'), new String_('city')),
            'state'   => new ArrayDimFetch(new Variable('addressArray'), new String_('state')),
        ];
        $properties = [
            'city'   => 'city',
            'state'   => 'state',
        ];
        $setters = [
            'city'   => new ArrayDimFetch(new Variable('addressArray'), new String_('city')),
            'state'   => new ArrayDimFetch(new Variable('addressArray'), new String_('state')),
        ];

        return [
            'address' => (new UsageTypeContext())
                ->setClass(null)
                ->setGetters($getters)
                ->setName('address')
                ->setNamespace('Tests\Fixture\Transformer\User')
                ->setObjects([])
                ->setPath(realpath(__DIR__.'/../../_support/Fixture/Transformer/User'))
                ->setProperties($properties)
                ->setSetters($setters)
                ->setUsage('array'),
        ];
    }
}
