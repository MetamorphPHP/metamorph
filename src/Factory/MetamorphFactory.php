<?php
declare(strict_types=1);

namespace Metamorph\Factory;

use Metamorph\Metamorph;
use Psr\Container\ContainerInterface;

final class MetamorphFactory
{
    public function __invoke(ContainerInterface $container): Metamorph
    {
        $config = (new MetamorphConfigFactory)($container->get('genData'));

        return new Metamorph($config);
    }
}