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

    public function from(string $usage): From
    {
        $this->resource->getContext()->setFrom($usage);

        return new From($this->resource);
    }

    public function properties(array $properties): Properties
    {
        $this->resource->getContext()->setProperties($properties);

        return new Properties($this->resource);
    }
}
