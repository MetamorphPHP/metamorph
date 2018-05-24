<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use Metamorph\Context\UsageTypeContext;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\VarLikeIdentifier;

class GenerateProperties
{
    public function __invoke(TransformerGeneratorContext $context)
    {
        $toContext = $context->getTo();
        if ($toContext->isClass()) {
            return [];
        }
        $statements = $this->createExcludedProperties($toContext);


        return $statements;
    }

    private function createExcludedProperties(UsageTypeContext $context)
    {
        $propertyNames[] = $context->getName();
        foreach ($context->getObjects() as $objectName => $objectContext) {
            $propertyNames[] = $objectName;
        }
        sort($propertyNames);

        $statements = [];
        foreach ($propertyNames as $name) {
            $statements[] = $this->getExcludeProperty($name);
        }

        return $statements;
    }

    private function getExcludeProperty($name): Property
    {
        $flags = Class_::MODIFIER_PRIVATE;
        $propertyName = 'excluded' . ucfirst($name) . 'Properties';
        $identifier = new VarLikeIdentifier($propertyName);
        $default = new Array_();
        $property = new PropertyProperty($identifier, $default);

        return new Property($flags, [$property]);
    }
}
