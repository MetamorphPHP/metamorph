<?php
declare(strict_types=1);

namespace Metamorph\Transformation;

use Metamorph\MetamorphTransformation;
use Ramsey\Uuid\Uuid;

class UuidToString implements MetamorphTransformation
{
    public function transform($from)
    {
        /** @var Uuid $from */
        return $from->toString();
    }
}
