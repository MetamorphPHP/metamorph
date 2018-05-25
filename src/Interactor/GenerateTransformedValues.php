<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use PhpParser\Node\Expr\Variable;

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
                    $variableName = $to->getObjects()[$property]->getVariableName();
                    $context->addSetValue($property, new Variable($variableName));
                } else {
                    $context->addSetValue($property, $getters[$property]);
                }

                continue;
            }

            $setVariableName = $to->getVariableName() . ucfirst($property);
            $context->addSetValue($property, new Variable($setVariableName));

            $statements = array_merge($statements, (new GenerateTransformationCode)($context, $property));
        }

        return $statements;
    }
}
