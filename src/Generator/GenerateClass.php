<?php
declare(strict_types=1);

namespace Metamorph\Generator;

use Metamorph\Context\TransformerGeneratorContext;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

class GenerateClass
{
    private $config;
    private $context;

    public function __invoke(TransformerGeneratorContext $context)
    {
        $this->context = $context;
        $this->config = $context->getConfig();

        $className = ucfirst($context->getFrom()) . 'To' . ucfirst($context->getTo()) . 'Transformer';

        $namespace = new PhpNamespace($this->config['namespace']);

        $class = new ClassType($className, $namespace);
        $class
            ->setExtends(AbstractTransformer::class)
            ->setFinal();

        $class->addMethod((new GenerateTransformMethod)($context));
    }
}
