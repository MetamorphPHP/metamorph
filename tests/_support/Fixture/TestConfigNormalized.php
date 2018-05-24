<?php
declare(strict_types=1);

namespace Tests\Fixture;

class TestConfigNormalized
{
    public static function get()
    {
        return [
            '_transformations' => [
                __DIR__.'/Transformation',
            ],
            'object' => [
                'user'    => [
                    'class'      => '\Tests\Fixture\TestUser',
                    'namespace'  => 'Tests\Fixture\Transformer',
                    'path'       => __DIR__.'/Transformer',
                    'properties' => [
                        'address'   => [
                            'name' => 'address',
                            'type' => [
                                'object' => 'address',
                            ],
                        ],
                        'allowed'   => [
                            'name' => 'allowed',
                            'type' => [
                                'scalar' => 'bool',
                            ],
                        ],
                        'birthday'  => [
                            'name' => 'birthday',
                            'type' => [
                                'class' => 'Carbon\Carbon',
                            ],
                        ],
                        'id'        => [
                            'name' => 'id',
                            'type' => [
                                'class' => 'Ramsey\Uuid',
                            ],
                        ],
                        'qualified' => [
                            'name' => 'qualified',
                            'type' => [
                                'scalar' => 'bool',
                            ],
                        ],
                        'username'  => [
                            'name' => 'username',
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                    ],
                ],
                'address' => [
                    'class'      => '\Tests\Fixture\TestAddress',
                    'path'       => __DIR__.'/Transformer/User',
                    'namespace'  => 'Tests\Fixture\Transformer\User',
                    'properties' => [
                        'city'  => [
                            'name' => 'city',
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                        'state' => [
                            'name' => 'state',
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                        'zip'   => [
                            'name' => 'zip',
                            'type' => [
                                'scalar' => 'string',
                            ],
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
                            'name' => 'address',
                            'type' => [
                                'object' => 'address',
                            ],
                        ],
                        'allowed'   => [
                            'name' => 'allowed',
                            'type' => [
                                'scalar' => 'bool',
                            ],
                        ],
                        'birthday'  => [
                            'name' => 'birth_day',
                            'type' => [
                                '_from' => ['transformer' => 'inclusiveDateTime'],
                                '_to' => ['format' => 'ISO8601'],
                            ],
                        ],
                        'id'        => [
                            'name' => '_id',
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                        'qualified' => [
                            'name' => 'qualified',
                            'type' => [
                                'scalar' => 'bool',
                            ],
                        ],
                        'username'  => [
                            'name' => 'username',
                            'type' => [
                                'scalar' => 'string',
                            ],
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
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                        'state' => [
                            'name' => 'state',
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
