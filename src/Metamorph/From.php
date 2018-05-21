<?php
declare(strict_types=1);

namespace Metamorph\Metamorph;

use Metamorph\ResourceInterface;

class From
{
    private $resource;

    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    public function to(string $usage)
    {
        $this->resource->getContext()->setTo($usage);

        return $this->resource->transform();
    }
}
