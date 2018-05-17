<?php
declare(strict_types=1);

namespace Metamorph\Factory;

use Exception;
use Metamorph\Context\UsageTypeContext;
use Metamorph\Interactor\PascalCase;

class UsageTypeContextFactory
{
    /** @var string */
    private $class;
    /** @var array */
    private $config;
    /** @var bool */
    private $isClass;
    /** @var array */
    private $properties;
    /** @var array */
    private $propertyTypes;
    /** @var string */
    private $type;
    /** @var string */
    private $usage;
    /** @var array */
    private $usageTypeConfig;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function create(string $usage, string $type): UsageTypeContext
    {
        $this->initData($usage, $type);
        $class = $this->getClass();
        $getters = $this->getGetters();
        $namespace = $this->getNamespace();
        $path = $this->getPath();
        $properties = $this->getProperties();
        $setters = $this->getSetters();

        return (new UsageTypeContext())
            ->setClass($class)
            ->setGetters($getters)
            ->setName($type)
            ->setNamespace($namespace)
            ->setPath($path)
            ->setProperties($properties)
            ->setSetters($setters)
            ->setUsage($usage);
    }

    private function getClass(): string
    {
        return $this->usageTypeConfig['class'];
    }

    private function getGetters(): array
    {
        $getters = [];
        foreach ($this->properties as $property => $name) {
            $getters[$property] = $this->generateGetter($name);
        }

        return $getters;
    }

    private function getNamespace(): string
    {
        return $this->usageTypeConfig['namespace'];
    }

    private function getPath(): string
    {
        return $this->usageTypeConfig['path'];
    }

    private function getProperties(): array
    {
        return $this->properties;
    }

    private function getSetters(): array
    {
        $setters = [];
        foreach ($this->properties as $property => $name) {
            $setters[$property] = $this->generateSetter($name);
        }

        return $setters;
    }

    private function generateGetter(string $name): string
    {
        if (! $this->isClass) {
            return '';
        }

        $getter = 'get' . (new PascalCase)($name);
        if (method_exists($this->class, $getter)) {
            return "\$$this->type->$getter();";
        }

        $isser = 'is' . (new PascalCase)($name);
        if (method_exists($this->class, $isser)) {
            return "\$$this->type->$isser();";
        }

        if (property_exists($this->class, $name)) {
            return "\$$this->type->$name";
        }

        throw new Exception("'$name' is not part of $this->class. Check the configuration or the class.");
    }

    private function generateSetter(string $name): string
    {
        if (! $this->isClass) {
            return '';
        }

        $setter = 'set' . (new PascalCase)($name);
        if (method_exists($this->class, $setter)) {
            return "\$$this->type->$setter(%_DATA_%);";
        }

        if (property_exists($this->class, $name)) {
            return "\$$this->type->$name = %_DATA_%;";
        }

        throw new Exception("'$name' is not part of $this->class. Check the configuration or the class.");
    }

    private function initData(string $usage, string $type)
    {
        $this->type = $type;
        $this->usage = $usage;
        if ('object' === $usage || 'objects' === $usage) {
            $usage = 'objects';
            $this->usageTypeConfig = $this->config['objects'][$type];
        } else {
            $this->usageTypeConfig = $this->config['transformers'][$usage][$type];
        }

        if (class_exists($this->usageTypeConfig['class'])) {
            $this->isClass = true;
            $this->class = $this->usageTypeConfig['class'];
        } else {
            $this->isClass = false;
        }
        $this->properties = [];
        $propertyConfigs = $this->config['objects'][$type]['properties'];
        foreach ($propertyConfigs as $propertyName => $propertyConfig) {
            $this->properties[$propertyName] = $propertyName;
            $type = $propertyConfig['type'] ?? 'string';
            $this->propertyTypes[$propertyName] = $type;
        }
        if ('objects' !== $usage) {
            foreach ($this->usageTypeConfig['properties'] as $name => $propertyConfig) {
            }
        }
    }
}
