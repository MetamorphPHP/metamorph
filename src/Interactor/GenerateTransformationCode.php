<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use Metamorph\Context\UsageTypeContext;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\ParserFactory;

class GenerateTransformationCode
{
    private $directories;
    /** @var UsageTypeContext */
    private $from;
    /** @var string */
    private $fromVariableName;
    /** @var array */
    private $fullyQualifiedNamedObjects;
    /** @var string */
    private $property;
    /** @var UsageTypeContext */
    private $to;
    /** @var string */
    private $toVariableName;

    public function __invoke(TransformerGeneratorContext $context, string $property)
    {
        $this->directories = $context->getConfig()['_transformations'];
        $this->directories[] = __DIR__.'/../Transformation';
        $this->property = $property;
        $this->from = $context->getFrom();
        $this->to = $context->getTo();
        $this->fromVariableName = $this->from->getVariableName().ucfirst($this->property);
        $this->toVariableName = $this->to->getVariableName().ucfirst($this->property);

        return $this->buildTransformation();
    }

    public function buildClassFileName()
    {
        return $this->getFromTransformation().'To'.$this->getToTransformation().'.php';
    }

    private function updateVariableNames(ClassMethod $method)
    {
        $methodVariableName = $method->params[0]->var->name;

        $replaceInNode = function ($parentNode) use (&$replaceInNode, $methodVariableName) {
            $subNodes = $parentNode->getSubNodeNames();
            if (in_array('expr', $subNodes)) {
                $replaceInNode($parentNode->expr);
            }
            if (in_array('args', $subNodes)) {
                foreach ($parentNode->args as $node) {
                    $replaceInNode($node);
                }
            }
            if (in_array('catches', $subNodes)) {
                foreach ($parentNode->catches as $node) {
                    $replaceInNode($node);
                }
            }
            if (in_array('cond', $subNodes)) {
                $replaceInNode($parentNode->cond);
            }
            if (in_array('else', $subNodes)) {
                if ($else = $parentNode->else) {
                    $replaceInNode($else);
                }
            }
            if (in_array('elseifs', $subNodes)) {
                foreach ($parentNode->elseifs as $node) {
                    $replaceInNode($node);
                }
            }
            if (in_array('finally', $subNodes)) {
                if ($finally = $parentNode->finally) {
                    $replaceInNode($finally);
                }
            }
            if (in_array('stmts', $subNodes)) {
                foreach ($parentNode->stmts as $position => $node) {
                    if ('Stmt_Return' === $node->getType()) {
                        $variable = new Variable($this->toVariableName);
                        $process = $node->expr;
                        $assign = new Assign($variable, $process);
                        $node = new Expression($assign);
                        $parentNode->stmts[$position] = $node;
                    } else {
                        $replaceInNode($node);
                    }
                }
            }
            if (in_array('types', $subNodes)) {
                foreach ($parentNode->types as $node) {
                    $replaceInNode($node);
                }
            }
            if (in_array('value', $subNodes)) {
                $value = $parentNode->value;
                if (!is_string($value)) {
                    $replaceInNode($value);
                }
            }
            if (in_array('var', $subNodes)) {
                $replaceInNode($parentNode->var);
            }
            if (in_array('class', $subNodes)) {
                $identifier = $parentNode->class->getLast();
                $parentNode->class = $this->fullyQualifiedNamedObjects[$identifier];
            }
            if (in_array('name', $subNodes)) {
                if ('Expr_Variable' === $parentNode->getType()) {
                    $variableName = $parentNode->name;
                    if ($variableName === $methodVariableName) {
                        $parentNode->name = $this->fromVariableName;
                    } else {
                        $expandedVariableName = $this->toVariableName.ucfirst($variableName);
                        $parentNode->name = $expandedVariableName;
                    }
                }
            }
        };

        $replaceInNode($method);
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

    private function extractObjectsFromUseStatements(Namespace_ $namespace)
    {
        $statements = $namespace->stmts;
        foreach ($statements as $node) {
            if ('Stmt_Use' === $node->getType()) {
                foreach ($node->uses as $useNode) {
                    $name = $useNode->name;
                    $identifier = $useNode->getAlias()->name;
                    $this->fullyQualifiedNamedObjects[$identifier] = new Name('\\'.$name->toString());
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

        return ucfirst(array_pop($config));
    }

    private function generate(array $ast): array
    {
        if (!$namespaceNode = $this->findNamespaceNode($ast)) {
            return [];
        }

        $this->extractObjectsFromUseStatements($namespaceNode);

        if (!$classNode = $this->findClassNode($namespaceNode)) {
            return [];
        }
        if (!$transformMethod = $this->findTransformMethodNode($classNode->stmts)) {
            return [];
        }

        $this->updateVariableNames($transformMethod);

        $variable = new Variable($this->fromVariableName);
        $assign = new Assign($variable, $this->from->getGetter($this->property));
        $statements[] = new Expression($assign);

        $statements = array_merge($statements, $transformMethod->stmts);

        return $statements;
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
