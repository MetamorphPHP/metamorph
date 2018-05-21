<?php
declare(strict_types=1);

namespace Metamorph\Metamorph;

use Metamorph\ResourceInterface;

class Properties
{
    private $resource;

    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    public function from(string $usage): From
    {
        $this->resource->getContext()->setFrom($usage);

        return new From($this->resource);
    }
}
