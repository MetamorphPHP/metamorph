<?php
return [
    'metamorph' => [
        'config'       => [
            'entities'        => [
                '_path'      => __DIR__.'/..',
                '_namespace' => 'Tests\Fixture',
            ],
            'transformers'    => [
                '_path'      => __DIR__.'/../Transformer',
                '_namespace' => 'Tests\Fixture\Transformer',
                'address'    => [
                    '_path'      => __DIR__.'/../Transformer/User',
                    '_namespace' => 'Tests\Fixture\Transformer\User',
                ],
            ],
            'usage'           => [
                'object' => [
                    'array' => [
                        'user',
                    ],
                ],
                'array'  => [
                    'object' => [
                        'user',
                    ],
                ],
            ],
        ],
        'objects'      => [
            'user'    => [
                'class'      => 'TestUser',
                'properties' => [
                    'address'         => [
                        'object' => 'address',
                    ],
                    'allowed'         => [
                        'scalar' => 'bool',
                    ],
                    'birthday'        => [
                        'class' => 'Carbon\Carbon',
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
                        'class' => 'Ramsey\Uuid',
                    ],
                    'username'        => [],
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
                    'zip'   => [
                        'scalar' => 'string',
                    ],
                ],
            ],
            'email'   => [
                'class'      => 'TestEmail',
                'properties' => [
                    'label' => [
                        'type' => 'string',
                    ],
                    'value' => [
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
                            '_from' => ['format' => 'inclusiveDateTime'],
                            '_to'   => ['format' => 'Iso8601'],
                            'name'  => 'birth_day',
                        ],
                        'favoriteNumbers' => [
                            'scalar'       => 'string',
                        ],
                        'id'       => [
                            'name'   => '_id',
                            'scalar' => 'string',
                        ],
                    ],
                ],
                'address' => [
                    'class'   => null,
                    'exclude' => [
                        'zip',
                    ],
                ],
                'email'   => [
                    'class' => null,
                ],
            ],
        ],
    ],
];
