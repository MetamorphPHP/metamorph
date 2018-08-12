<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use Metamorph\Context\UsageTypeContext;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;

class UpdateVariableNamesInMethod
{
    /** @var UsageTypeContext */
    private $from;
    /** @var string */
    private $fromVariableName;
    /** @var array */
    private $fullyQualifiedParts;
    /** @var string */
    private $property;
    /** @var UsageTypeContext */
    private $to;
    /** @var string */
    private $toVariableName;
    /** @var string[] */
    private $replacementVariableNames;

    public function __construct(TransformerGeneratorContext $context, string $property)
    {
        $this->property = $property;
        $this->from = $context->getFrom();
        $this->to = $context->getTo();
        $this->fromVariableName = $this->from->getVariableNameForProperty($property);
        $this->toVariableName = $this->to->getVariableNameForProperty($property);
    }

    public function update(Namespace_ $namespace, ClassMethod $method)
    {
        $this->setNamespaceNode($namespace);

        $methodVariableName = $method->params[0]->var->name;

        $this->replacementVariableNames[$methodVariableName] = $this->fromVariableName;

        $updatedMethod = $this->replaceInNode($method);

        $variable = new Variable($this->fromVariableName);
        $assign = new Assign($variable, $this->from->getGetter($this->property));
        $statements[] = new Expression($assign);

        $statements = array_merge($statements, $updatedMethod->stmts);

        return $statements;
    }

    private function replaceInChildren($parentNode, $properties)
    {
        $returnNode = clone $parentNode;
        $subNodes = $parentNode->getSubNodeNames();
        foreach ($properties as $property) {
            if (in_array($property, $subNodes)) {
                foreach ($parentNode->$property as $position => $node) {
                    $returnNode->$property[$position] = $this->replaceInNode($node);
                }
            }
        }

        return $returnNode;
    }

    private function replaceFullyQualified($parentNode)
    {
        $returnNode = clone $parentNode;

        if ('Name' === $parentNode->getType()) {
            $identifier = $parentNode->getLast();
            $returnNode = new FullyQualified($this->fullyQualifiedParts[$identifier]);
        }

        return $returnNode;
    }

    private function replaceVariable($parentNode)
    {
        $returnNode = clone $parentNode;

        if ('Expr_Variable' === $parentNode->getType()) {
            $variableName = $parentNode->name;
            if (!isset($this->replacementVariableNames[$variableName])) {
                $expandedVariableName = $this->toVariableName.ucfirst($variableName);
                $this->replacementVariableNames[$variableName] = $expandedVariableName;
            }
            $returnNode->name = $this->replacementVariableNames[$variableName];
        }

        return $returnNode;
    }

    private function replaceInNode($parentNode)
    {
        $returnNode = $this->replaceInSingleChild($parentNode, ['cond', 'class', 'else', 'expr', 'finally', 'var']);
        $returnNode = $this->replaceInChildren($returnNode, ['args', 'catches', 'elseifs', 'types']);
        $returnNode = $this->replaceInStmts($returnNode);
        $returnNode = $this->replaceInValue($returnNode);
        $returnNode = $this->replaceFullyQualified($returnNode);
        $returnNode = $this->replaceVariable($returnNode);

        return $returnNode;
    }

    private function replaceInSingleChild($parentNode, $properties)
    {
        $returnNode = clone $parentNode;
        $subNodes = $parentNode->getSubNodeNames();
        foreach ($properties as $property) {
            if (in_array($property, $subNodes)) {
                if (!empty($parentNode->$property)) {
                    $returnNode->$property = $this->replaceInNode($parentNode->$property);
                }
            }
        }

        return $returnNode;
    }

    private function replaceInStmts($parentNode)
    {
        $returnNode = clone $parentNode;
        $subNodes = $parentNode->getSubNodeNames();

        if (in_array('stmts', $subNodes)) {
            foreach ($parentNode->stmts as $position => $node) {
                if ('Stmt_Return' === $node->getType()) {
                    $variable = new Variable($this->toVariableName);
                    $process = $this->replaceInNode($node->expr);
                    $assign = new Assign($variable, $process);
                    $node = new Expression($assign);
                    $returnNode->stmts[$position] = $node;
                } else {
                    $returnNode->stmts[$position] = $this->replaceInNode($node);
                }
            }
        }

        return $returnNode;
    }

    private function replaceInValue($parentNode)
    {
        $returnNode = clone $parentNode;
        $subNodes = $parentNode->getSubNodeNames();

        if (in_array('value', $subNodes)) {
            $value = $parentNode->value;
            if (!is_string($value)) {
                $returnNode->value = $this->replaceInNode($value);
            }
        }

        return $returnNode;
    }

    private function setNamespaceNode(Namespace_ $namespace)
    {
        $statements = $namespace->stmts;
        foreach ($statements as $node) {
            if ('Stmt_Use' === $node->getType()) {
                foreach ($node->uses as $useNode) {
                    $name = $useNode->name;
                    $identifier = $useNode->getAlias()->name;
                    $fullyQualifiedName = new FullyQualified($name->toString());
                    $this->fullyQualifiedParts[$identifier] = $fullyQualifiedName->parts;
                }
            }
        }
    }
}