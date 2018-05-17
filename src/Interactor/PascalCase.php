<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

class PascalCase
{
    public function __invoke(string $string)
    {
        return str_replace(['_', '-'], '', ucwords($string, '_-'));
    }
}
