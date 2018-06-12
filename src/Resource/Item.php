<?php
declare(strict_types=1);

namespace Metamorph\Resource;

class Item extends AbstractResource
{
    public function getValue()
    {
        return $this->data;
    }

    public function transform()
    {
        $transformer = $this->context->getTransformer();

        return $transformer->transform($this);
    }
}