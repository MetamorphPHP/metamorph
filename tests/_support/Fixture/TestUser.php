<?php
declare(strict_types=1);

namespace Tests\Fixture;

use Ramsey\Uuid\Uuid;

class TestUser
{
    public $birthday;

    /** @var bool */
    private $allowed;
    /** @var Uuid */
    private $id;
    /** @var bool */
    private $qualified;
    /** @var string */
    private $username;

    public function isAllowed(): bool
    {
        return $this->allowed;
    }

    public function setAllowed(bool $allowed): TestUser
    {
        $this->allowed = $allowed;

        return $this;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): TestUser
    {
        $this->id = $id;

        return $this;
    }

    public function getQualified(): bool
    {
        return $this->qualified;
    }

    public function setQualified(bool $qualified): TestUser
    {
        $this->qualified = $qualified;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): TestUser
    {
        $this->username = $username;

        return $this;
    }
}
