<?php
declare(strict_types=1);

namespace Metamorph\Transformation;

use Metamorph\MetamorphTransformation;

class StringToNullableString implements MetamorphTransformation
{
    public function transform($from)
    {
        return $from ?? null;
    }
}
