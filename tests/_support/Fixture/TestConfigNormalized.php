<?php
declare(strict_types=1);

namespace Tests\Fixture;

class TestConfigNormalized
{
    public static function get()
    {
        return [
            'object' => [
                'user'    => [
                    'class'      => TestUser::class,
                    'namespace'  => 'Tests\Fixture\Transformer',
                    'path'       => __DIR__.'/Transformer',
                    'properties' => [
                        'address'   => [
                            'name'   => 'address',
                            'object' => 'address',
                        ],
                        'allowed'   => [
                            'name' => 'allowed',
                            'type' => 'bool',
                        ],
                        'birthday'  => [
                            'name' => 'birthday',
                            'type' => 'Carbon\Carbon',
                        ],
                        'id'        => [
                            'name' => 'id',
                            'type' => 'Ramsey\Uuid',
                        ],
                        'qualified' => [
                            'name' => 'qualified',
                            'type' => 'bool',
                        ],
                        'username'  => [
                            'name' => 'username',
                            'type' => 'string',
                        ],
                    ],
                ],
                'address' => [
                    'class'      => TestAddress::class,
                    'path'       => __DIR__.'/Transformer/User',
                    'namespace'  => 'Tests\Fixture\Transformer\User',
                    'properties' => [
                        'city'  => [
                            'name' => 'city',
                            'type' => 'string',
                        ],
                        'state' => [
                            'name' => 'state',
                            'type' => 'string',
                        ],
                        'zip' => [
                            'name' => 'zip',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            'array'  => [
                'user'    => [
                    'class'      => null,
                    'namespace'  => 'Tests\Fixture\Transformer',
                    'path'       => __DIR__.'/Transformer',
                    'properties' => [
                        'address'   => [
                            'name'   => 'address',
                            'object' => 'address',
                        ],
                        'allowed'   => [
                            'name' => 'allowed',
                            'type' => 'bool',
                        ],
                        'birthday'  => [
                            'name' => 'birth_day',
                            'type' => 'ISO8601',
                        ],
                        'id'        => [
                            'name' => '_id',
                            'type' => 'string',
                        ],
                        'qualified' => [
                            'name' => 'qualified',
                            'type' => 'bool',
                        ],
                        'username'  => [
                            'name' => 'username',
                            'type' => 'string',
                        ],
                    ],
                ],
                'address' => [
                    'class'      => null,
                    'path'       => __DIR__.'/Transformer/User',
                    'namespace'  => 'Tests\Fixture\Transformer\User',
                    'properties' => [
                        'city'  => [
                            'name' => 'city',
                            'type' => 'string',
                        ],
                        'state' => [
                            'name' => 'state',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
        ];
    }
}
