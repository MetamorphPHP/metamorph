<?php
declare(strict_types=1);

namespace Metamorph\Resource;

abstract class AbstractResource
{
    protected $context;

    public function __construct()
    {
        $this->context = new ResourceContext();
    }

    public function getContext(): ResourceContext
    {
        return $this->context;
    }

    public function getExcludedProperties(string $type): array
    {
        return [];
    }

    abstract public function getValue();

    abstract public function transform(): array;
}
