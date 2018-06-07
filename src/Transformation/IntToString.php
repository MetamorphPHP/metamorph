<?php
declare(strict_types=1);

namespace Metamorph\Transformation;

use Metamorph\MetamorphTransformation;

class IntToString implements MetamorphTransformation
{
    public function transform($from)
    {
        return (string) $from;
    }
}