<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;

class GenerateTransformedValues
{
    public function __invoke(TransformerGeneratorContext $context)
    {
        $from = $context->getFrom();
        $to = $context->getTo();

        $getters = $from->getGetters();
        $fromTypes = $from->getTypes();
        $toTypes = $to->getTypes();

        $properties = $to->getProperties();

        $statements = [];

        foreach ($properties as $property => $name) {
            if (!isset($fromTypes[$property])) {
                continue;
            }
            if ($fromTypes[$property] === $toTypes[$property]) {
                if (isset($fromTypes[$property]['object'])) {
                    $variableName = $to->getVariableNameForProperty($property);

                    $context->addSetValue($property, new Variable($variableName));
                } else {
                    $context->addSetValue($property, $getters[$property]);
                }

                continue;
            }

            $fromType = $fromTypes[$property];
            if (true === $fromType['isCollection']) {
                $transformationStatements = [];
                $setVariableName = $to->getVariableNameForProperty($property);
                $context->addSetValue($property, new Variable($setVariableName));

                $fromVariable = new Variable($from->getVariableNameForProperty($property));
                $assign = new Assign($fromVariable, $from->getGetter($property));
                $transformationStatements[] = new Expression($assign);

                $toVariable = new Variable($to->getVariableNameForProperty($property));
                $assign = new Assign($toVariable, new Array_());
                $transformationStatements[] = new Expression($assign);

                $asVariable = $from->getForVariableForProperty($property);

                $assignVariable = $to->getForVariableForProperty($property);
                $context->getFrom()->setVariableNameForProperty($property, $asVariable->name);
                $context->getTo()->setVariableNameForProperty($property, $assignVariable->name);
                $forStatements = (new GenerateTransformationCode)($context, $property);
                array_shift($forStatements); // todo: refactor other cases so GenerateTransformationCode doesn't get te

                $toAssignment = new ArrayDimFetch($toVariable);
                $forAssignment = new Assign($toAssignment, $assignVariable);
                $forStatements[] = new Expression($forAssignment);

                $subNodes = [
                    'stmts' => $forStatements,
                ];
                $transformationStatements[] = new Foreach_($fromVariable, $asVariable, $subNodes);
            } else {
                $setVariableName = $to->getVariableNameForProperty($property);
                $context->addSetValue($property, new Variable($setVariableName));
                $transformationStatements = (new GenerateTransformationCode)($context, $property);
            }

            $statements = array_merge($statements, $transformationStatements);
        }

        return $statements;
    }
}
