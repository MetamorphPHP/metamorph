<?php
declare(strict_types=1);

namespace Metamorph;

interface TransformerInterface
{
    public function transform(ResourceInterface $resource);
}
