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
                        '_path'      => __DIR__.'/../../_support/Fixture',
                        '_namespace' => 'Tests\Fixture',
                    ],
                    'transformers' => [
                        '_path'      => __DIR__.'/../../_support/Fixture/Transformer',
                        '_namespace' => 'Tests\Fixture\Transformer',
                        'address'    => [
                            '_path'      => __DIR__.'/../../_support/Fixture/Transformer/User',
                            '_namespace' => 'Tests\Fixture\Transformer\User',
                        ],
                    ],
                    'transformations' => [
                        __DIR__.'/Transformation',
                    ],
                    'usage' => [
                        'object' => [
                            'array',
                        ],
                        'array' => [
                            'object'
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
                                'scalar' => 'bool',
                            ],
                            'birthday'  => [
                                'class' => 'Carbon\Carbon',
                            ],
                            'id'        => [
                                'class' => 'Ramsey\Uuid',
                            ],
                            'qualified' => [
                                'scalar' => 'bool',
                            ],
                            'username'  => [],
                        ],
                    ],
                    'address' => [
                        'class'      => 'TestAddress',
                        'properties' => [
                            'city'  => [
                                'scalar' => 'string',
                            ],
                            'state' => [
                                'scalar' => 'string',
                            ],
                            'zip' => [
                                'scalar' => 'string',
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
                                    '_from' => ['format' => 'inclusiveDateTime'],
                                    '_to' => ['format' => 'ISO8601'],
                                    'name' => 'birth_day',
                                ],
                                'id'       => [
                                    'name' => '_id',
                                    'scalar' => 'string',
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
