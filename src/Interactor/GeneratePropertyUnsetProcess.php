<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;

class GeneratePropertyUnsetProcess
{
    public function __invoke(TransformerGeneratorContext $context)
    {
        $to = $context->getTo();
        if ($to->isClass()) {
            return [];
        }

        $statements = [];

        return $statements;
    }
}
