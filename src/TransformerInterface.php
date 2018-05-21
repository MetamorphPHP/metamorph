<?php
declare(strict_types=1);

namespace Metamorph;

use Metamorph\Resource\AbstractResource;

interface TransformerInterface
{
    public function transform(AbstractResource $resource);
}
