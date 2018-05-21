<?php
declare(strict_types=1);

namespace Metamorph\Factory;

use Exception;
use Metamorph\Context\TransformerType;
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
    private $objects = [];
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
    /** @var string */
    private $variableName;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function createFrom(TransformerType $type)
    {
        return $this->create($type->getFrom(), $type->getType());
    }

    public function createTo(TransformerType $type)
    {
        return $this->create($type->getTo(), $type->getType());
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
            ->setObjects($this->objects)
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
        if (!$this->isClass) {
            return new ArrayDimFetch(new Variable($this->variableName), new String_($name));
        }

        $getter = 'get'.(new PascalCase)($name);
        if (method_exists($this->class, $getter)) {
            return new MethodCall(new Variable($this->variableName), new Identifier($getter));
        }

        $isser = 'is'.(new PascalCase)($name);
        if (method_exists($this->class, $isser)) {
            return new MethodCall(new Variable($this->variableName), new Identifier($isser));
        }

        if (property_exists($this->class, $name)) {
            return new PropertyFetch(new Variable($this->variableName), new Identifier($name));
        }

        throw new Exception("'$name' is not part of $this->class. Check the configuration or the class.");
    }

    private function generateSetter(string $name)
    {
        if (!$this->isClass) {
            return new ArrayDimFetch(new Variable($this->variableName), new String_($name));
        }

        $setter = 'set'.(new PascalCase)($name);
        if (method_exists($this->class, $setter)) {
            return [new Variable($this->variableName), new Identifier($setter)];
        }

        if (property_exists($this->class, $name)) {
            return new PropertyFetch(new Variable($this->variableName), new Identifier($name));
        }

        throw new Exception("'$name' is not part of $this->class. Check the configuration or the class.");
    }

    private function initData(string $usage, string $type)
    {
        $this->type = $type;
        $this->usage = $usage;
        $this->variableName = $type.ucfirst($usage);
        $this->usageTypeConfig = $this->config[$usage][$type];
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
        $this->initProperties();
    }

    private function initProperties()
    {
        $this->properties = [];
        foreach ($this->usageTypeConfig['properties'] as $propertyName => $propertyConfig) {
            $this->properties[$propertyName] = $propertyConfig['name'];
            $type = $propertyConfig['type'] ?? ['object' => $propertyConfig['object']];

            if (isset($propertyConfig['object'])) {
                $this->addObject($propertyConfig['object']);
            }
            $this->propertyTypes[$propertyName] = $type;
        }
    }

    private function addObject(string $object)
    {
        $factory = new UsageTypeContextFactory($this->config);

        $this->objects[$object] = $factory->create($this->usage, $object);
    }
}
