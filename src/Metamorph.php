<?php
declare(strict_types=1);

namespace Metamorph;

use Metamorph\Metamorph\Transform;
use Metamorph\Resource\AbstractResource;

class Metamorph
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function transform(AbstractResource $resource): Transform
    {
        $resource->getContext()->setMetamorph($this);

        return new Transform($resource);
    }
}
