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
        foreach ($this->data as $datum) {
            $this->currentData = $datum;
        }
    }
}