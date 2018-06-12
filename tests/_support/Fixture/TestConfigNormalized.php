<?php
declare(strict_types=1);

namespace Tests\Fixture;

class TestConfigNormalized
{
    public static function get()
    {
        return [
            '_usage' => [
                'object' => [
                    'array' => [
                        'user',
                    ],
                ],
                'array' => [
                    'object' => [
                        'user',
                    ],
                ],
            ],
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
                            'isCollection' => false,
                            'name' => 'address',
                            'type' => [
                                'object' => 'address',
                            ],
                        ],
                        'allowed'   => [
                            'isCollection' => false,
                            'name' => 'allowed',
                            'type' => [
                                'scalar' => 'bool',
                            ],
                        ],
                        'birthday'  => [
                            'isCollection' => false,
                            'name' => 'birthday',
                            'type' => [
                                'class' => 'Carbon\Carbon',
                            ],
                        ],
                        'email'   => [
                            'isCollection' => true,
                            'name' => 'email',
                            'type' => [
                                'object' => 'email',
                            ],
                        ],
                        'favoriteNumbers'   => [
                            'isCollection' => true,
                            'name' => 'favoriteNumbers',
                            'type' => [
                                'scalar' => 'int',
                            ],
                        ],
                        'id'        => [
                            'isCollection' => false,
                            'name' => 'id',
                            'type' => [
                                'class' => 'Ramsey\Uuid',
                            ],
                        ],
                        'username'  => [
                            'isCollection' => false,
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
                            'isCollection' => false,
                            'name' => 'city',
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                        'state' => [
                            'isCollection' => false,
                            'name' => 'state',
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                        'zip'   => [
                            'isCollection' => false,
                            'name' => 'zip',
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                    ],
                ],
                'email' => [
                    'class'      => '\Tests\Fixture\TestEmail',
                    'path'       => __DIR__.'/Transformer',
                    'namespace'  => 'Tests\Fixture\Transformer',
                    'properties' => [
                        'label' => [
                            'isCollection' => false,
                            'name' => 'label',
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                        'value' => [
                            'isCollection' => false,
                            'name' => 'value',
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
                            'isCollection' => false,
                            'name' => 'address',
                            'type' => [
                                'object' => 'address',
                            ],
                        ],
                        'allowed'   => [
                            'isCollection' => false,
                            'name' => 'allowed',
                            'type' => [
                                'scalar' => 'bool',
                            ],
                        ],
                        'birthday'  => [
                            'isCollection' => false,
                            'name' => 'birth_day',
                            'type' => [
                                '_from' => ['format' => 'inclusiveDateTime'],
                                '_to' => ['format' => 'Iso8601'],
                            ],
                        ],
                        'email'   => [
                            'isCollection' => true,
                            'name' => 'email',
                            'type' => [
                                'object' => 'email',
                            ],
                        ],
                        'favoriteNumbers'   => [
                            'isCollection' => true,
                            'name' => 'favoriteNumbers',
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                        'id'        => [
                            'isCollection' => false,
                            'name' => '_id',
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                        'username'  => [
                            'isCollection' => false,
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
                            'isCollection' => false,
                            'name' => 'city',
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                        'state' => [
                            'isCollection' => false,
                            'name' => 'state',
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                    ],
                ],
                'email' => [
                    'class'      => null,
                    'path'       => __DIR__.'/Transformer',
                    'namespace'  => 'Tests\Fixture\Transformer',
                    'properties' => [
                        'label' => [
                            'isCollection' => false,
                            'name' => 'label',
                            'type' => [
                                'scalar' => 'string',
                            ],
                        ],
                        'value' => [
                            'isCollection' => false,
                            'name' => 'value',
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
