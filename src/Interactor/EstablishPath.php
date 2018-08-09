<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

final class EstablishPath
{
    public function __invoke(string $path): bool
    {
        if (is_dir($path)) {
            return true;
        }
        
        return mkdir($path, 0777, true);
    }
}