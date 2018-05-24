<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;

class GetSetStatements
{
    public function __invoke(TransformerGeneratorContext $context)
    {
        $statements = (new GetChildValues)($context);
        $statements[] = (new GenerateObjectInitialization)($context);
        $statements = array_merge($statements, (new GenerateTransformedValues)($context));
        $statements = array_merge($statements, (new GenerateSetToValues)($context));
        $statements = array_merge($statements, (new GeneratePropertyUnsetProcess)($context));

        return $statements;
    }
}
