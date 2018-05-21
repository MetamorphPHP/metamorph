<?php
declare(strict_types=1);

namespace Metamorph;

use Metamorph\Metamorph\Transform;

class Metamorph
{
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function transform(ResourceInterface $resource): Transform
    {
        $resource->getContext()->setMetamorph($this);

        return new Transform($resource);
    }
}
