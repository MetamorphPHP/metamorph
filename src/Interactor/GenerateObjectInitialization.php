<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use Metamorph\Context\UsageTypeContext;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;

class GenerateObjectInitialization
{
    public function __invoke(TransformerGeneratorContext $context)
    {
        $to = $context->getTo();
        if ($to->isClass()) {
            return $this->createObjectInitializer($to);
        } else {
            return $this->createArrayInitializer($to);
        }
    }

    private function createArrayInitializer(UsageTypeContext $context): Expression
    {
        $variableName = $context->getVariableName();
        $variable = new Variable($variableName);

        $emptyArray = new Array_();

        $assign = new Assign($variable, $emptyArray);

        return new Expression($assign);
    }

    private function createObjectInitializer(UsageTypeContext $context): Expression
    {
        $variableName = $context->getVariableName();
        $variable = new Variable($variableName);

        $newObject = new New_(new Name($context->getClass()));

        $assign = new Assign($variable, $newObject);

        return new Expression($assign);
    }
}
