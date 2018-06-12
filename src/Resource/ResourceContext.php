<?php
declare(strict_types=1);

namespace Metamorph\Resource;

use Closure;
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
    /** @var string */
    private $type;

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
        $getNamespace = function ($type, $to) {
            return $this->config[$to][$type]['namespace'];
        };

        $namespace = Closure::bind($getNamespace, $this->metamorph, $this->metamorph)->__invoke($this->type, $this->to);

        $className = $namespace . '\\' . ucfirst($this->type) . ucfirst($this->from) . 'To' . ucfirst($this->to) . 'Transformer';

        return new $className;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): ResourceContext
    {
        $this->type = $type;

        return $this;
    }
}
