<?php
declare(strict_types=1);

namespace Metamorph\Factory;

use Metamorph\Context\TransformerGeneratorContext;
use Metamorph\Context\TransformerType;
use Metamorph\Context\UsageTypeContext;

class TransformerGeneratorContextFactory
{
    private $config;
    /** @var UsageTypeContextFactory */
    private $usageContextFactory;
    /** @var UsageTypeContext[] */
    private $usages = [];

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->usageContextFactory = new UsageTypeContextFactory($config);
    }

    public function create(TransformerType $transformerType): TransformerGeneratorContext
    {
        $fromContext = $this->usageContextFactory->createFrom($transformerType);
        $toContext = $this->usageContextFactory->createTo($transformerType);

        return (new TransformerGeneratorContext())
            ->setConfig($this->config)
            ->setFrom($fromContext)
            ->setTo($toContext);
    }
}
