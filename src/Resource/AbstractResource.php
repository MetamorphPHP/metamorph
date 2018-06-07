<?php
declare(strict_types=1);

namespace Metamorph\Resource;

abstract class AbstractResource
{
    protected $context;
    protected $data;

    public function __construct($data)
    {
        $this->context = new ResourceContext();
        $this->data = $data;
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

    abstract public function transform();
}
