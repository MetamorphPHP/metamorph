<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use Metamorph\Context\UsageTypeContext;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;

class GetChildValues
{
    public function __invoke(TransformerGeneratorContext $context)
    {
        $statements = [];

        $fromObjects = $context->getFrom()->getObjects();
        $toObjects = $context->getTo()->getObjects();

        foreach ($toObjects as $property => $toContext) {
            $fromContext = $fromObjects[$property];

            $childContext = (new TransformerGeneratorContext())
                ->setFrom($fromContext)
                ->setTo($toContext);

            if ($fromContext->isClass()) {
                $statements[] = $this->assignInitialFromObject($property, $context->getFrom(), $fromContext);
            } else {
                $statements[] = $this->assignInitialFromArray($property, $context->getFrom(), $fromContext);
            }
            $statements = array_merge($statements, (new GetSetStatements)($childContext));
        }

        return $statements;
    }

    private function assignInitialFromArray(string $property, UsageTypeContext $parent, UsageTypeContext $child): Expression
    {
        $fromVariable = new Variable($parent->getVariableNameForProperty($property));
        $assign = new Assign($fromVariable, $parent->getGetter($property));

        return new Expression($assign);
    }

    private function assignInitialFromObject(string $property, UsageTypeContext $parent, UsageTypeContext $child): Expression
    {

    }
}
