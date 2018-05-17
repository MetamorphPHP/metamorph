<?php
declare(strict_types=1);

namespace Tests\Unit\Generator;

use Metamorph\Context\TransformerGeneratorContext;
use UnitTester;

class GenerateSetterCest
{
    public function __construct()
    {
        $this->context = (new TransformerGeneratorContext())
            ->setConfig()
            ->setObject()
            ->setFrom()
            ->setTo();
    }

    public function testGenerateSetterMethod(UnitTester $I)
    {
        $context = $this->context->getContextForProperty('id');

        $code = (new GenerateSetterCode())->generate($context);
    }

    public function testGenerateSetterProperty(UnitTester $I)
    {

    }

    public function testGenerateSetArrayKey(UnitTester $I)
    {

    }
}
