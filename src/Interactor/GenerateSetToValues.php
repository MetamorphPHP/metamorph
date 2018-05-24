<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use Metamorph\Context\UsageTypeContext;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Expression;

class GenerateSetToValues
{
    public function __invoke(TransformerGeneratorContext $context)
    {
        $values = $context->getSetValues();
        $to = $context->getTo();
        if ($to->isClass()) {
            return $this->setObjectToValues($to, $values);
        }

        return $this->setArrayToValues($to, $values);
    }

    private function setArrayToValues(UsageTypeContext $to, $values)
    {
        $setters = $to->getSetters();
        $properties = $to->getProperties();
        $statements = [];

        foreach ($properties as $property => $name) {
            if (empty($values[$property])) {
                continue;
            }
            $setter = $setters[$property];
            $value = $values[$property];
            $assign = new Assign($setter, $value);
            $statements[] = new Expression($assign);
        }

        return $statements;
    }

    private function setObjectToValues(UsageTypeContext $to, $values)
    {
        $setters = $to->getSetters();
        $properties = $to->getProperties();
        $statements = [];

        foreach ($properties as $property => $name) {
            if (empty($values[$property])) {
                continue;
            }
            $setter = $setters[$property];
            $value = $values[$property];
            if (is_array($setter)) {
                $assign = new MethodCall($setter[0], $setter[1], [$value]);
            } else {
                $assign = new Assign($setter, $value);
            }
            $statements[] = new Expression($assign);
        }

        return $statements;
    }
}
