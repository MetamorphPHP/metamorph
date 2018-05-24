<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;

class GenerateTransformMethod
{
    public function __invoke(TransformerGeneratorContext $context)
    {
        $params = [
            new Param(new Variable('resource'), null, 'AbstractResource'),
        ];

        $statements = $this->getExpressions($context);

        $methodName = new Identifier('transform');

        $subNodes = [
            'flags'  => Class_::MODIFIER_PUBLIC,
            'params' => $params,
            'stmts'  => $statements,
        ];

        return new ClassMethod($methodName, $subNodes);
    }

    private function getExpressions(TransformerGeneratorContext $context): array
    {
        $statements = [];

        $statements[] = $this->getSourceValue($context);

        $statements = array_merge($statements, (new GetSetStatements)($context));
//
//        $statements[] = $this->getReturnStatement($context);

        return $statements;
    }

    private function getSourceValue(TransformerGeneratorContext $context): Expression
    {
        $from = $context->getFrom();

        $variableName = $from->getVariableName();
        $variable = new Variable($variableName);
        $resourceName = new Variable('resource');
        $method = new Identifier('getValue');
        $resourceMethod = new MethodCall($resourceName, $method);
        $assign = new Assign($variable, $resourceMethod);

        return new Expression($assign);
    }
}
