<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\PrettyPrinter\Standard;

class GenerateClass
{
    private $config;

    public function __invoke(TransformerGeneratorContext $context)
    {
        $this->config = $context->getConfig();

        $statements = $this->getNamespaceStatements($context);

        $namespaceName = new Name($context->getFrom()->getNamespace());
        $namespace = new Namespace_($namespaceName, $statements);

        $prettyPrinter = new Standard(['shortArraySyntax' => true]);
        $results = $prettyPrinter->prettyPrintFile([$namespace]);

        (new EstablishPath)($context->getTo()->getPath());
        
        $filePath = $context->getTo()->getPath() . '/' . $this->getClassNameString($context) . '.php';

        file_put_contents($filePath, $results);
    }

    private function getClassName(TransformerGeneratorContext $context): Name
    {
        return new Name($this->getClassNameString($context));
    }

    private function getClassNameString(TransformerGeneratorContext $context): string
    {
        return ucfirst($context->getTo()->getName()).
            ucfirst($context->getFrom()->getUsage()).
            'To'.
            ucfirst($context->getTo()->getUsage()).
            'Transformer';
    }

    private function getClassStatements(TransformerGeneratorContext $context): array
    {
        $classProperties = (new GenerateProperties)($context);
        $classMethods = (new GetTransformMethods)($context);

        return array_merge($classProperties, $classMethods);
    }

    private function getNamespaceStatements(TransformerGeneratorContext $context): array
    {
        $statements = (new GenerateUseStatements)($context);

        $className = $this->getClassName($context);
        $subNodes = [
            'implements' => [new Name('TransformerInterface')],
            'stmts'      => $this->getClassStatements($context),
        ];
        $statements[] = new Class_($className, $subNodes);

        return $statements;
    }
}
