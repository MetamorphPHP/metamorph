<?php
declare(strict_types=1);

namespace Tests\Fixture;

class TestConfig
{
    public static function get()
    {
        return include __DIR__ . '/config/test.php';
    }
}
