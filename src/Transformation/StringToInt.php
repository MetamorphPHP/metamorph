<?php
declare(strict_types=1);

namespace Metamorph\Transformation;

use Metamorph\MetamorphTransformation;

class StringToInt implements MetamorphTransformation
{
    public function transform($from)
    {
        return (int) $from;
    }
}