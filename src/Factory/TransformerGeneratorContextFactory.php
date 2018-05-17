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
        $toContext = $this->getUsageFor($transformerType->getTo());
        $fromContext = $this->getUsageFor($transformerType->getFrom());

        return (new TransformerGeneratorContext())
            ->setFrom($fromContext)
            ->setTo($toContext);
    }

    private function buildUsage(string $usage)
    {

        $this->usages[$usage] = $usage;
    }

    private function getUsageFor(string $usage): UsageTypeContext
    {
        if (!isset($this->usages[$usage])) {
            $this->buildUsage($usage);
        }

        return $this->usages[$usage];
    }
}
