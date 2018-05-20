<?php
declare(strict_types=1);

namespace Metamorph;

use Metamorph\Interactor\PascalCase;

class Metamorph
{
    /** @var string */
    private $from;
    private $resource;
    /** @var string */
    private $to;
    /** @var array */
    private $transformers;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function transform(Resource $resource): Metamorph
    {
        $this->resource = $resource;

        return $this;
    }

    public function from(string $usage): Metamorph
    {
        $this->from = $usage;

        return $this;
    }

    public function to(string $usage)
    {
        $this->to = $usage;

        return $this->doTransform();
    }

    private function buildTransformer()
    {
        $usage = $this->resource->getUsage();

        $namespace = $this->config[$usage]['object']['namespace'];

        $className = ucfirst($usage) . ucfirst($this->from) . 'To' . ucfirst($this->to) . 'Transformer';

        $this->transformers[$usage][$this->from][$this->to] = $namespace . '\\' . $className;
    }
    
    private function doTransform()
    {
        $transformer = $this->getTransformer();


    }

    private function getTransformer()
    {
        $usage = $this->resource->getUsage();
        
        if (empty($this->transformers[$usage][$this->from][$this->to])) {
            $this->buildTransformer($usage);
        }

        $transformerClass = $this->transformers[$usage][$this->from][$this->to];

        return new $transformerClass;
    }
}
