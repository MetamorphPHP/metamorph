<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;


class GetTransformMethods
{
    public function __invoke(TransformerGeneratorContext $context)
    {
        $statement = [];

        $toContext = $context->getTo();

        $statement[] = (new GenerateTransformMethod)($context);
        $statement[] = (new GenerateSetExclusionsMethod)($toContext);

        return $statement;
    }
}
