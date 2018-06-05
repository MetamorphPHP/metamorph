<?php
declare(strict_types=1);

namespace Tests\Fixture;

class TestEmail
{
    /** @var string */
    private $label;
    /** @var string */
    private $value;

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): TestEmail
    {
        $this->label = $label;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): TestEmail
    {
        $this->value = $value;

        return $this;
    }
}
