<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Unset_;

class GeneratePropertyUnsetProcess
{
    public function __invoke(TransformerGeneratorContext $context): array
    {
        $to = $context->getTo();
        if ($to->isClass()) {
            return [];
        }

        $thisVariable = new Variable('this');
        $identifier = new Identifier($to->getExcludedPropertyName());
        $propertyFetch = new PropertyFetch($thisVariable, $identifier);

        $propertyToUnset = new Variable('propertyToUnset');
        $unsetVariables[] = new ArrayDimFetch(new Variable($to->getVariableName()), $propertyToUnset);
        $statements[] = new Unset_($unsetVariables);
        $subNodes = [
            'stmts' => $statements,
        ];

        return [new Foreach_($propertyFetch, $propertyToUnset, $subNodes)];
    }
}
