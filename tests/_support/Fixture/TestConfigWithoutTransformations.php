<?php
declare(strict_types=1);

namespace Tests\Fixture;

class TestConfigWithoutTransformations
{
    public static function get()
    {
        return include __DIR__ . '/config/no_transformations.php';
    }
}
