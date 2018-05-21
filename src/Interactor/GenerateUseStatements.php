<?php
declare(strict_types=1);

namespace Metamorph\Interactor;

use Metamorph\Context\TransformerGeneratorContext;
use Metamorph\Resource\AbstractResource;
use Metamorph\TransformerInterface;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;

class GenerateUseStatements
{
    public function __invoke(TransformerGeneratorContext $context)
    {
        $uses = [
            [
                'name' => AbstractResource::class,
            ],
            [
                'name' => TransformerInterface::class,
            ],
        ];
        $statements = [];
        foreach ($uses as $use) {
            $statements[] = new Use_([new UseUse(new Name($use['name']), $use['alias'] ?? null)]);
        }

        return $statements;
    }
}
