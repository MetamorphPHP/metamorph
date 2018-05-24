<?php
declare(strict_types=1);

namespace Metamorph;

interface MetamorphTransformation
{
    public function transform($from);
}
