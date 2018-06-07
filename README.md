Metamorph
=========

Transform your data

Usage

```php

$transformer = new Metamorph($config);

$resource = new Collection($incomingData);

$transformedData = $transformer->transform($resource)->from('object')->to('response');

```
