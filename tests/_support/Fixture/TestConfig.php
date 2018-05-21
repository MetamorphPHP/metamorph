<?php
declare(strict_types=1);

namespace Tests\Fixture;

class TestConfig
{
    public static function get()
    {
        return [
            'genData' => [
                'config'       => [
                    'entities' => [
                        'path'      => __DIR__.'/../../_support/Fixture',
                        'namespace' => 'Tests\Fixture',
                    ],
                    'transformers' => [
                        'path'      => __DIR__.'/../../_support/Fixture/Transformer',
                        'namespace' => 'Tests\Fixture\Transformer',
                        'address'    => [
                            'path'      => __DIR__.'/../../_support/Fixture/Transformer/User',
                            'namespace' => 'Tests\Fixture\Transformer\User',
                        ],
                    ],
                ],
                'objects'      => [
                    'user'    => [
                        'class'      => 'TestUser',
                        'properties' => [
                            'address'   => [
                                'object' => 'address',
                            ],
                            'allowed'   => [
                                'type' => 'bool',
                            ],
                            'birthday'  => [
                                'type' => 'Carbon\Carbon',
                            ],
                            'id'        => [
                                'type' => 'Ramsey\Uuid',
                            ],
                            'qualified' => [
                                'type' => 'bool',
                            ],
                            'username'  => [],
                        ],
                    ],
                    'address' => [
                        'class'      => 'TestAddress',
                        'properties' => [
                            'city'  => [
                                'type' => 'string',
                            ],
                            'state' => [
                                'type' => 'string',
                            ],
                            'zip' => [
                                'type' => 'string',
                            ],
                        ],
                    ],
                ],
                'transformers' => [
                    'array' => [
                        'user'    => [
                            'class'      => null,
                            'properties' => [
                                'birthday' => [
                                    'type' => 'ISO8601',
                                    'name' => 'birth_day',
                                ],
                                'id'       => [
                                    'type' => 'string',
                                ],
                            ],
                        ],
                        'address' => [
                            'class' => null,
                            'exclude' => [
                                'zip',
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }
}
