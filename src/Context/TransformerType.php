<?php
declare(strict_types=1);

namespace Metamorph\Context;

class TransformerType
{
    /** @var string */
    private $from;
    /** @var string */
    private $to;
    /** @var string */
    private $type;

    public function getFrom(): string
    {
        return $this->from;
    }

    public function setFrom(string $from): TransformerType
    {
        if ('object' === $from) {
            $from = 'objects';
        }
        $this->from = $from;

        return $this;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function setTo(string $to): TransformerType
    {
        if ('object' === $to) {
            $to = 'objects';
        }
        $this->to = $to;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): TransformerType
    {
        $this->type = $type;

        return $this;
    }
}
