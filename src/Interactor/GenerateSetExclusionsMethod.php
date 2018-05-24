<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\UsageTypeContext;
use Metamorph\Resource\AbstractResource;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;

class GenerateSetExclusionsMethod
{
    public function __invoke(UsageTypeContext $context)
    {
        $propertyNames[] = $context->getName();
        foreach ($context->getObjects() as $objectName => $objectContext) {
            $propertyNames[] = $objectName;
        }
        sort($propertyNames);

        $statements = [];
        if (!$context->isClass()) {
            foreach ($propertyNames as $name) {
                $statements[] = $this->getExcludeExpression($name);
            }
        }

        $methodName = new Identifier('setExclusions');

        $params = [
            new Param(new Variable('resource'), null, 'AbstractResource'),
        ];

        $subNodes = [
            'flags'  => Class_::MODIFIER_PUBLIC,
            'params' => $params,
            'stmts'  => $statements,
        ];

        return new ClassMethod($methodName, $subNodes);
    }

    private function getExcludeExpression($name): Expression
    {
        $propertyName = 'excluded'.ucfirst($name).'Properties';
        $identifier = new Identifier($propertyName);
        $thisProperty = new PropertyFetch(new Variable('this'), $identifier);

        $resourceVariable = new Variable('resource');
        $resourcIdentifier = new Identifier('getExcludedProperties');
        $args = [
            new Arg(new String_($name)),
        ];
        $resourceMethod = new MethodCall($resourceVariable, $resourcIdentifier, $args);
        $assign = new Assign($thisProperty, $resourceMethod);

        return new Expression($assign);
    }
}
