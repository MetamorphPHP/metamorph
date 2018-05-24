<?php
declare(strict_types=1);

namespace Metamorph\Transformation;

use Carbon\Carbon;
use Metamorph\MetamorphTransformation;

class CarbonToIso8601 implements MetamorphTransformation
{
    public function transform($from)
    {
        /** @var Carbon $from */
        return $from->toIso8601String();
    }
}
