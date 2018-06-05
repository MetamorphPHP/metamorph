<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use Metamorph\Context\UsageTypeContext;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\ParserFactory;

class GenerateTransformationCode
{
    private $directories;
    /** @var UsageTypeContext */
    private $from;
    /** @var string */
    private $property;
    /** @var UsageTypeContext */
    private $to;
    private $updateVariableNames;

    public function __invoke(TransformerGeneratorContext $context, string $property)
    {
        $this->directories = $context->getConfig()['_transformations'];
        $this->directories[] = __DIR__.'/../Transformation';
        $this->property = $property;
        $this->from = $context->getFrom();
        $this->to = $context->getTo();
        $this->updateVariableNames = new UpdateVariableNamesInMethod($context, $property);

        return $this->buildTransformation();
    }

    private function buildClassFileName()
    {
        return $this->getFromTransformation().'To'.$this->getToTransformation().'.php';
    }

    private function buildTransformation(): array
    {
        $classFilename = $this->buildClassFileName();
        foreach ($this->directories as $directory) {
            $filename = $directory.'/'.$classFilename;
            if (file_exists($filename)) {
                $contents = file_get_contents($filename);

                $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
                $ast = $parser->parse($contents);

                return $this->generate($ast);
            }
        }

        return [];
    }

    private function findClassNode(Namespace_ $namespace): Class_
    {
        $nodes = $namespace->stmts;

        foreach ($nodes as $node) {
            if ('Stmt_Class' === $node->getType()) {
                return $node;
            }
        }
    }

    private function findNamespaceNode(array $ast): ?Namespace_
    {
        foreach ($ast as $node) {
            if ('Stmt_Namespace' === $node->getType()) {
                return $node;
            }
        }
    }

    private function findTransformMethodNode(array $ast): ?ClassMethod
    {
        foreach ($ast as $node) {
            if ('Stmt_ClassMethod' === $node->getType()) {
                if ('transform' === $node->name->name) {
                    return $node;
                }
            }
        }
    }

    private function extractTypeFromConfig(array $config)
    {
        if (isset($config['class'])) {
            $className = $config['class'];
            $parts = explode('\\', $className);

            return ucfirst(array_pop($parts));
        }
        unset($config['isCollection']);

        return ucfirst(array_pop($config));
    }

    private function generate(array $ast): array
    {
        if (!$namespaceNode = $this->findNamespaceNode($ast)) {
            return [];
        }
        if (!$classNode = $this->findClassNode($namespaceNode)) {
            return [];
        }
        if (!$transformMethod = $this->findTransformMethodNode($classNode->stmts)) {
            return [];
        }

        return $this->updateVariableNames->update($namespaceNode, $transformMethod);
    }

    private function getFromTransformation()
    {
        $typeConfig = $this->from->getTypes()[$this->property];

        if (isset($typeConfig['_from'])) {
            return $this->extractTypeFromConfig($typeConfig['_from']);
        }

        return $this->extractTypeFromConfig($typeConfig);

    }

    private function getToTransformation()
    {
        $typeConfig = $this->to->getTypes()[$this->property];

        if (isset($typeConfig['_to'])) {
            return $this->extractTypeFromConfig($typeConfig['_to']);
        }

        return $this->extractTypeFromConfig($typeConfig);
    }
}