<?php
declare(strict_types=1);

namespace Metamorph\Metamorph;

use Metamorph\Resource\AbstractResource;

class Transform
{
    private $resource;

    public function __construct(AbstractResource $resource)
    {
        $this->resource = $resource;
    }

    public function as(string $type): AsType
    {
        $this->resource->getContext()->setType($type);

        return new AsType($this->resource);
    }

    public function properties(array $properties): Properties
    {
        $this->resource->getContext()->setProperties($properties);

        return new Properties($this->resource);
    }
}
