<?php
declare(strict_types=1);

namespace Metamorph\Resource;

class Collection extends AbstractResource
{
    private $currentData;

    public function getValue()
    {
        return $this->currentData;
    }

    public function transform()
    {
        $transformer = $this->context->getTransformer();
        $transformed = [];
        foreach ($this->data as $datum) {
            $this->currentData = $datum;

            $transformed[] = $transformer->transform($this);
        }

        return $transformed;
    }
}