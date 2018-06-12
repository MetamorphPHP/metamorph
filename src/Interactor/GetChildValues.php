<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use Metamorph\Context\UsageTypeContext;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;

class GetChildValues
{
    public function __invoke(TransformerGeneratorContext $context)
    {
        $statements = [];

        $from = $context->getFrom();
        $to = $context->getTo();
        $fromObjects = $from->getObjects();
        $toObjects = $context->getTo()->getObjects();

        foreach ($toObjects as $property => $toContext) {
            $fromContext = $fromObjects[$property];

            $childContext = (new TransformerGeneratorContext())
                ->setFrom($fromContext)
                ->setTo($toContext);

            $propertyStatements = [];

            $propertyType = $context->getTo()->getTypes()[$property];
            if (true === $propertyType['isCollection']) {
                $fromVariable = new Variable($from->getVariableNameForProperty($property));
                $assign = new Assign($fromVariable, $from->getGetter($property));

                $propertyStatements[] = new Expression($assign);

                $toVariable = new Variable($to->getVariableNameForProperty($property));
                $assign = new Assign($toVariable, new Array_());
                $propertyStatements[] = new Expression($assign);

                $asVariable = $from->getForVariableForProperty($property);

                $assignVariable = $to->getForVariableForProperty($property);
                $childContext->getTo()->setVariable($assignVariable);
                $forStatements = (new GetSetStatements)($childContext);

                $toAssignment = new ArrayDimFetch($toVariable);
                $forAssignment = new Assign($toAssignment, $assignVariable);
                $forStatements[] = new Expression($forAssignment);

                $subNodes = [
                    'stmts' => $forStatements,
                ];
                $propertyStatements[] = new Foreach_($fromVariable, $asVariable, $subNodes);
            } else {
                $propertyStatements[] = $this->assignInitial($property, $context->getFrom());
                $setVariable = $to->getVariableForProperty($property);
                $childContext->getTo()->setVariable($setVariable);
                $propertyStatements = array_merge($propertyStatements, (new GetSetStatements)($childContext));
            }

            $statements = array_merge($statements, $propertyStatements);
        }

        return $statements;
    }

    private function assignInitial(string $property, UsageTypeContext $parent): Expression
    {
        $fromVariable = new Variable($parent->getVariableNameForProperty($property));
        $assign = new Assign($fromVariable, $parent->getGetter($property));

        return new Expression($assign);
    }
}
