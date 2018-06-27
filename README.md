Metamorph
=========

Transform your data

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
    'objects'      => [
      'user'    => [
        'class'      => 'TestUser',
      ],
    ],
  ],
];
```
You'll need to identify the properties in the class, do that with a `properties` key and an associative array of properties. The name of the property should be the key and the attributes of the property are contained in that key. You can have a `scalar` value, like a `string` or `int`. That would be configured like this
```php
$config = [
  'genData' => 
    'objects'      => [
      'user'    => [
        'class'      => 'TestUser',
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
$config = [
  'genData' => 
    'objects'      => [
      'user'    => [
        'class'      => 'User',
        'properties' => [
          'allowed' => [
            'scalar' => 'bool',
          ],
          'email' => [
            'object' => 'email',
          ],
        ],
      ],
      'email'  => [
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
