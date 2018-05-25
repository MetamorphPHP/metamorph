<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use Metamorph\Context\UsageTypeContext;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
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
        $this->fromVariableName = $this->from->getVariableName().ucfirst($this->property);
        $this->toVariableName = $this->to->getVariableName().ucfirst($this->property);
    }

    public function update(Namespace_ $namespace, ClassMethod $method)
    {
        $this->setNamespaceNode($namespace);

        $methodVariableName = $method->params[0]->var->name;

        $this->replacementVariableNames[$methodVariableName] = $this->fromVariableName;

        $this->replaceInNode($method);

        $variable = new Variable($this->fromVariableName);
        $assign = new Assign($variable, $this->from->getGetter($this->property));
        $statements[] = new Expression($assign);

        $statements = array_merge($statements, $method->stmts);

        return $statements;
    }

    private function replaceInChildren($parentNode, $properties)
    {
        $subNodes = $parentNode->getSubNodeNames();
        foreach ($properties as $property) {
            if (in_array($property, $subNodes)) {
                foreach ($parentNode->$property as $node) {
                    $this->replaceInNode($node);
                }
            }
        }
    }

    private function replaceFullyQualified($parentNode)
    {
        if ('Name' === $parentNode->getType()) {
            $identifier = $parentNode->getLast();
            $parentNode->parts = $this->fullyQualifiedParts[$identifier];
        }
    }

    private function replaceVariable($parentNode)
    {
        $subNodes = $parentNode->getSubNodeNames();

            if ('Expr_Variable' === $parentNode->getType()) {
                $variableName = $parentNode->name;
                if (!isset($this->replacementVariableNames[$variableName])) {
                    $expandedVariableName = $this->toVariableName.ucfirst($variableName);
                    $this->replacementVariableNames[$variableName] = $expandedVariableName;
                }
                $parentNode->name = $this->replacementVariableNames[$variableName];
            }
    }

    private function replaceInNode($parentNode)
    {
        $this->replaceInSingleChild($parentNode, ['cond', 'class', 'else', 'expr', 'finally', 'var']);
        $this->replaceInChildren($parentNode, ['args', 'catches', 'elseifs', 'types']);
        $this->replaceInStmts($parentNode);
        $this->replaceInValue($parentNode);
        $this->replaceFullyQualified($parentNode);
        $this->replaceVariable($parentNode);
    }

    private function replaceInSingleChild($parentNode, $properties)
    {
        $subNodes = $parentNode->getSubNodeNames();
        foreach ($properties as $property) {
            if (in_array($property, $subNodes)) {
                if (!empty($parentNode->$property)) {
                    $this->replaceInNode($parentNode->$property);
                }
            }
        }
    }

    private function replaceInStmts($parentNode)
    {
        $subNodes = $parentNode->getSubNodeNames();

        if (in_array('stmts', $subNodes)) {
            foreach ($parentNode->stmts as $position => $node) {
                if ('Stmt_Return' === $node->getType()) {
                    $variable = new Variable($this->toVariableName);
                    $process = $node->expr;
                    $assign = new Assign($variable, $process);
                    $node = new Expression($assign);
                    $parentNode->stmts[$position] = $node;
                } else {
                    $this->replaceInNode($node);
                }
            }
        }
    }

    private function replaceInValue($parentNode)
    {
        $subNodes = $parentNode->getSubNodeNames();

        if (in_array('value', $subNodes)) {
            $value = $parentNode->value;
            if (!is_string($value)) {
                $this->replaceInNode($value);
            }
        }
    }

    private function setNamespaceNode(Namespace_ $namespace)
    {
        $statements = $namespace->stmts;
        foreach ($statements as $node) {
            if ('Stmt_Use' === $node->getType()) {
                foreach ($node->uses as $useNode) {
                    $name = $useNode->name;
                    $identifier = $useNode->getAlias()->name;
                    $fullyQualifiedName = new Name('\\'. $name->toString());
                    $this->fullyQualifiedParts[$identifier] = $fullyQualifiedName->parts;
                }
            }
        }
    }
}