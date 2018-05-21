<?php
declare(strict_types=1);

namespace Metamorph;

use Metamorph\Resource\ResourceContext;

interface ResourceInterface
{
    public function getContext(): ResourceContext;

    public function getUsage(): string;

    public function getValue();

    public function transform(): array;
}
