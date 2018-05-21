Metamorph
=========

Transform your data

Usage

```php

$transformer = new Metamorph($config);

$resource = new Collection($data);

$data = $transformer->transform($resource)->from('object')->to('response);


```
