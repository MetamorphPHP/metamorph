<?php
declare(strict_types=1);

namespace Tests\Fixture;

class TestConfig
{
    public static function get()
    {
        return [
            'objects' => [
                'path'       => __DIR__.'/../../_support/Fixture/Transformer',
                'namespace'  => 'Tests\Fixture\Transformer',
                'user' => [
                    'class'      => TestUser::class,
                    'properties' => [
                        'address' => [
                            'object' => 'address',
                        ],
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
                'address' => [
                    'path'       => __DIR__.'/../../_support/Fixture/Transformer/User',
                    'namespace'  => 'Tests\Fixture\Transformer\User',
                    'class' => TestAddress::class,
                    'properties' => [
                        'city' => [
                            'type' => 'string',
                        ],
                        'state' => [
                            'type' => 'string',
                        ],
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
                    'address' => [
                        'class' => null,
                    ]
                ]
            ],
        ];
    }
}
