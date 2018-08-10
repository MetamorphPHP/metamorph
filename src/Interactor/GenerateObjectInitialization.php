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

final class GenerateObjectInitialization
{
    /** @var Variable */
    private $variable;

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
        $emptyArray = new Array_();

        $assign = new Assign($context->getVariable(), $emptyArray);

        return new Expression($assign);
    }

    private function createObjectInitializer(UsageTypeContext $context): Expression
    {
        $newObject = new New_(new Name('\\' . $context->getClass()));

        $assign = new Assign($context->getVariable(), $newObject);

        return new Expression($assign);
    }
}
