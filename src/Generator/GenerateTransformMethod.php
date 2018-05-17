<?php
declare(strict_types=1);

namespace Metamorph\Generator;

use Metamorph\Context\TransformerGeneratorContext;
use Nette\PhpGenerator\Method;

class GenerateTransformMethod
{
    private $config;
    private $context;

    public function __invoke(TransformerGeneratorContext $context)
    {
        $this->config = $context->getProperties('object');
        $properties = $this->buildProperties();
        foreach () {

        }
        $method = new Method('transform');

    }
}
