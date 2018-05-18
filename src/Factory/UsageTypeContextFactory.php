<?php
declare(strict_types=1);

namespace Metamorph\Factory;

use Exception;
use Metamorph\Context\UsageTypeContext;
use Metamorph\Interactor\PascalCase;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;

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

    private function getClass(): ?string
    {
        return $this->usageTypeConfig['class'];
    }

    private function getGetters(): array
    {
        $getters = [];
        foreach ($this->properties as $identifier => $name) {
            $getters[$identifier] = $this->generateGetter($name);
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
        foreach ($this->properties as $identifier => $name) {
            $setters[$identifier] = $this->generateSetter($name);
        }

        return $setters;
    }

    private function generateGetter(string $name)
    {
        if (! $this->isClass) {
            return new ArrayDimFetch(new Variable($this->type), new String_($name));
        }

        $getter = 'get' . (new PascalCase)($name);
        if (method_exists($this->class, $getter)) {
            return new MethodCall(new Variable($this->type), new Identifier($getter));
        }

        $isser = 'is' . (new PascalCase)($name);
        if (method_exists($this->class, $isser)) {
            return new MethodCall(new Variable($this->type), new Identifier($isser));
        }

        if (property_exists($this->class, $name)) {
            return new PropertyFetch(new Variable($this->type), new Identifier($name));
        }

        throw new Exception("'$name' is not part of $this->class. Check the configuration or the class.");
    }

    private function generateSetter(string $name)
    {
        if (! $this->isClass) {
            return new ArrayDimFetch(new Variable($this->type), new String_($name));
        }

        $setter = 'set' . (new PascalCase)($name);
        if (method_exists($this->class, $setter)) {
            return [new Variable($this->type), new Identifier($setter)];
        }

        if (property_exists($this->class, $name)) {
            return new PropertyFetch(new Variable($this->type), new Identifier($name));
        }

        throw new Exception("'$name' is not part of $this->class. Check the configuration or the class.");
    }

    private function initData(string $usage, string $type)
    {
        $this->type = $type;
        $this->usage = $usage;
        $baseConfig = $this->config['objects'][$type];
        if ('object' === $usage || 'objects' === $usage) {
            $usage = 'objects';
            $this->usageTypeConfig = $baseConfig;
        } else {
            $this->usageTypeConfig = array_replace_recursive($baseConfig, $this->config['transformers'][$usage][$type]);
        }

        if (!$class = $this->usageTypeConfig['class']) {
            $this->isClass = false;
        } else {
            if (class_exists($class)) {
                $this->isClass = true;
                $this->class = $class;
            } else {
                throw new Exception("$class doesn't exist");
            }
        }
        $this->initProperties($usage, $type);
    }

    private function initProperties(string $usage, string $type)
    {
        $this->properties = [];
        $propertyConfigs = $this->config['objects'][$type]['properties'];
        foreach ($propertyConfigs as $propertyName => $propertyConfig) {
            $this->properties[$propertyName] = $propertyName;
            $type = $propertyConfig['type'] ?? 'string';
            $this->propertyTypes[$propertyName] = $type;
        }
        if ('objects' !== $usage) {
            foreach ($this->usageTypeConfig['properties'] as $identifier => $propertyConfig) {
                if (isset($propertyConfig['name'])) {
                    $this->properties[$identifier] = $propertyConfig['name'];
                }
            }
        }
    }
}
