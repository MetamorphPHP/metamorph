<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\UsageTypeContext;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;

class GenerateCollection
{
    public function __invoke(string $property, array $forStatements, UsageTypeContext $from, UsageTypeContext $to)
    {
        $statements[] = array_shift($forStatements);
        $toVariable = new Variable($to->getVariableNameForProperty($property));
        $assign = new Assign($toVariable, new Array_());
        $statements[] = new Expression($assign);

        $fromVariable = new Variable($from->getVariableNameForProperty($property));
        $asName = $property . ucfirst($from->getUsage());
        $asVariable = new Variable($asName);

        $toAssignment = new ArrayDimFetch($toVariable);
        $assignName = $property . ucfirst($to->getUsage());
        $assignVariable = new Variable($assignName);
        $forAssignment = new Assign($toAssignment, $assignVariable);
        $forStatements[] = new Expression($forAssignment);

        $subNodes = [
            'stmts' => $forStatements,
        ];
        $statements[] = new Foreach_($fromVariable, $asVariable, $subNodes);

        return $statements;
    }
}