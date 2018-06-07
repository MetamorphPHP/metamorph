<?php
declare(strict_types=1);

namespace Metamorph\Resource;

use Metamorph\Metamorph;
use Metamorph\TransformerInterface;

class ResourceContext
{
    /** @var string */
    private $from;
    /** @var Metamorph */
    private $metamorph;
    /** @var array */
    private $properties;
    /** @var string */
    private $to;

    public function getFrom(): string
    {
        return $this->from;
    }

    public function setFrom(string $from): ResourceContext
    {
        $this->from = $from;

        return $this;
    }

    public function getMetamorph(): Metamorph
    {
        return $this->metamorph;
    }

    public function setMetamorph(Metamorph $metamorph): ResourceContext
    {
        $this->metamorph = $metamorph;

        return $this;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): ResourceContext
    {
        $this->properties = $properties;

        return $this;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function setTo(string $to): ResourceContext
    {
        $this->to = $to;

        return $this;
    }

    public function getTransformer(): TransformerInterface
    {

    }
}
