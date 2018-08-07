Metamorph
=========

Transform your data

Install
-------
```
composer require metamorph/metamorph
```

Usage
-----
```php

$transformer = new Metamorph($config);

$resource = new Collection($incomingData);

$transformedData = $transformer->transform($resource)->as('user')->from('object')->to('response');

```
Getting Started
---------------
You will use a YAML file so set up the configuration of the transformers. The configuration should be in array with the key `genData` and an array of config values. The incoming data can be transformed into an array or into an object. 

Your default objects are set in the `objects` value. It is assumed the default is an object and has an associated class. First, set a reference name for the object, in this case `user`.To configure the class for the object, use a `class` value. The name of the class is NOT fully qualified. That gets handled elsewhere in the configuration. You configuration would look like this so far
```php
$config = [
  'genData' => 
    'objects' => [
      'user' => [
        'class' => 'TestUser',
      ],
    ],
  ],
];
```
You'll need to identify the properties in the class, do that with a `properties` key and an associative array of properties. The name of the property should be the key and the attributes of the property are contained in that key. You can have a `scalar` value, like a `string` or `int`. That would be configured like this
```php
$config = [
  'genData' => [
    'objects' => [
      'user' => [
        'class' => 'TestUser',
        'properties' => [
          'allowed' => [
            'scalar' => 'bool',
          ],
        ],
      ],
    ],
  ],
];
```
The property could be the value of a configured object, like an email. The config would look like this
```php
$config = [
  'genData' => [
    'objects' => [
      'user' => [
        'class' => 'User',
        'properties' => [
          'allowed' => [
            'scalar' => 'bool',
          ],
          'email' => [
            'object' => 'email',
          ],
        ],
      ],
      'email' => [
        'class' => 'Email',
        'properties' => [
          'label' => [
            'scalar' => 'string',
          ],
          'value' => [
            'scalar' => 'string',
          ],  
        ],
      ],
    ],
  ],
];
```
If you wanted to have more than one email address in the property, you could set `isCollection` to `true`. You configuration would now look like this.
```php
$config = [
  'genData' => [
    'objects' => [
      'user' => [
        'class' => 'User',
        'properties' => [
          'allowed' => [
            'scalar' => 'bool',
          ],
          'email' => [
            'isCollection' => true,
            'object' => 'email',
          ],
        ],
      ],
      'email' => [
        'class' => 'Email',
        'properties' => [
          'label' => [
            'scalar' => 'string',
          ],
          'value' => [
            'scalar' => 'string',
          ],  
        ],
      ],
    ],
  ],
];
```
The `email` property of the `User` class will now be treated like an array in the generated transformer.

The final type that a property could be is a fully qualified class. An example might be using a uuid for the id of the class. You would have a `class` key with the fully qualified class name one of the property attributes.
```php
$config = [
  'genData' => [
    'objects' => [
      'user' => [
        'class' => 'User',
        'properties' => [
          'allowed' => [
            'scalar' => 'bool',
          ],
          'email' => [
            'isCollection' => true,
            'object' => 'email',
          ],
          'id'=> [
              'class' => \Ramsey\Uuid\Uuid::class,
          ],
        ],
      ],
      'email' => [
        'class' => 'Email',
        'properties' => [
          'label' => [
            'scalar' => 'string',
          ],
          'value' => [
            'scalar' => 'string',
          ],  
        ],
      ],
    ],
  ],
];
```
You'll want to transform the data from and to different sources. You might have the data come in an http request as JSON that is decoded to an array. The client side may have different property name. For example, the client side property is called `is_allowed` instead of `allowed`. We are going to need to have configuration for transformation. We do that with a `transformer` key and an array of usages. For this case, we'll call the useage `request`. Our config would look like this
```php
$config = [
  'genData' => [
    'objects' => [ ... ],
    'transformers' => [
      'request' => [
        'class' => null,
        'properties' => [
          'allowed' => [
            'name' => 'is_allowed'
          ],
        ],
      ],
    ],
  ],
];
```
Notice the class type was set to `null`. This means the request is expected to be an array.

You may need to have the proprety value transformed from one type to another. These types be `class`es, `object`s, or `scalar`s, like with the object configuration. An example of this might be the uuid being a string an not an object when it is in the request.
```php
$config = [
  'genData' => [
    'objects' => [ ... ],
    'transformers' => [
      'request' => [
        'class' => null,
        'properties' => [
          'allowed' => [
            'name' => 'is_allowed'
          ],
          'id' => [
            'scalar' => 'string'
          ],
        ],
      ],
    ],
  ],
];
```
A value can be transformed in different ways, depending on the the direction the transformation is happening. For example, for a date, you might be willing to transform from any format coming in, but only an ISO8601 on the way out. You would configure that like this.
```php
$config = [
  'genData' => [
    'objects' => [ ... ],
    'transformers' => [
      'request' => [
        'class' => null,
        'properties' => [
          'allowed' => [
            'name' => 'is_allowed'
          ],
          'birthday' => [
            '_from' => ['format' => 'inclusiveDateTime'],
            '_to' => ['format' => 'ISO8601'],
          ],
          'id' => [
            'scalar' => 'string'
          ],
        ],
      ],
    ],
  ],
];
```
But wait! There is no birthday in the object! That's ok, when the `request` is being tranformed to the `object`, the data will just be ignored.
Any properties that have the same property name and data type can be ignored in the transformer configuration. If there are any properties in the object that should not be included in the tranformed usage, you can simply `exclude` them. 

```php
$config = [
  'genData' => [
    'objects' => [ ... ],
    'transformers' => [
      'request' => [
        'class' => null,
        'exclude' => [
          'email',
        ],
        'properties' => [
          'allowed' => [
            'name' => 'is_allowed'
          ],
          'birthday' => [
            '_from' => ['format' => 'inclusiveDateTime'],
            '_to' => ['format' => 'ISO8601'],
          ],
          'id' => [
            'scalar' => 'string'
          ],
        ],
      ],
    ],
  ],
];
```

Configure for Generation
------------------------

```php
$config = [
  'genData' => [
    'objects' => [ ... ],
    'transformers' => [ ... ],
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
    ],
  ],
];
```

`transformations` are directories to classes that will covert between data types and formats.
`usages` are the ways data can be transformed. It is configured by setting the usage as the key and the value is an array
of usages it can get transformed to. In each of the usages is the object label of what is tobe transformed.

Generate the Transformers
-------------------------

```
morph generate
```

This will read from `resources/metamorph` by default. If you want to read from a different directory then
you can set the path in the command

```
morph generate --path=/home/path/config
```

Documentation to write still ...
-----
* Transformations
* configuration for classes, target namespace and location for transformers, location of transformations, usage configuration

To Do:
-----
* allow for specified properties to be returned, like happens in a GraphQL query.
