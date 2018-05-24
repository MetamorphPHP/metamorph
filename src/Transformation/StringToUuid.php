<?php
declare(strict_types=1);

namespace Metamorph\Transformation;

use Metamorph\MetamorphTransformation;
use Ramsey\Uuid\Uuid;

class StringToUuid implements MetamorphTransformation
{
    public function transform($from)
    {
        return Uuid::fromString($from);
    }
}
