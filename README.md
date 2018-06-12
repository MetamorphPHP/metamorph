Metamorph
=========

Transform your data

Usage

```php

$transformer = new Metamorph($config);

$resource = new Collection($incomingData);

$transformedData = $transformer->transform($resource)->as('user')->from('object')->to('response');

```
