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
        $transformerTypes = [];
        $usages = $this->config['_usage'];
        foreach ($usages as $from => $to) {
            foreach ($to as $toName => $types) {
                $transformerTypes = array_merge($transformerTypes, $this->getTypesFromConfig($from, $toName, $types));
            }
        }

        return $transformerTypes;
    }

    private function getTypesFromConfig(string $from, string $to, $types)
    {
        $transformerTypes = [];

        if (is_array($types)) {
            foreach ($types as $type) {
                $transformerTypes[] = (new TransformerType())
                    ->setFrom($from)
                    ->setTo($to)
                    ->setType($type);

            }
        }

        return $transformerTypes;
    }
}
