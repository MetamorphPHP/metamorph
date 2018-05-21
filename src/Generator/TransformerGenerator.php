<?php
declare(strict_types=1);

namespace Metamorph\Generator;

use Metamorph\Context\TransformerType;
use Metamorph\Factory\TransformerGeneratorContextFactory;
use Metamorph\Interactor\GenerateClass;

class TransformerGenerator
{
    private $config;
    private $contextFactory;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->contextFactory = (new TransformerGeneratorContextFactory($config));
    }

    public function generate()
    {
        $transformerTypes = $this->getTransformerTypes();
        foreach ($transformerTypes as $transformerType) {
            $this->generateType($transformerType);
        }
    }

    public function generateType(TransformerType $transformerType)
    {
        $context = $this->contextFactory->create($transformerType);
        $class = (new GenerateClass)($context);
    }

    private function getConfig(TransformerType $transformerType)
    {

    }

    /**
     * @return TransformerType[]
     */
    private function getTransformerTypes(): array
    {
        $types = [];
        $usages = $this->config['transformers']['_usage'];
        foreach ($usages as $type => $transformations) {
            foreach ($transformations as $from => $to) {
                if (is_array($to)) {
                    foreach ($to as $individualTo) {
                        $types[] = (new TransformerType())
                            ->setFrom($from)
                            ->setTo($individualTo)
                            ->setType($type);
                    }
                } else {
                    $types[] = (new TransformerType())
                        ->setType($type)
                        ->setFrom($from)
                        ->setTo($to);
                }
            }
        }
    }
}
