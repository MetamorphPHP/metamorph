<?php
declare(strict_types=1);

namespace Metamorph\Factory;

use InvalidArgumentException;

class MetamorphConfigFactory
{
    private $entityClasses;
    private $namespaces;
    private $objects;
    private $paths;
    private $transformations;
    private $usages;

    public function __invoke(array $config): array
    {
        $genConfig = $config['metamorph'];

        $this->setConfig($genConfig);

        $normalized = [];
        $normalized['_transformations'] = $this->transformations;
        $normalized['_usage'] = $this->usages;
        foreach ($genConfig['objects'] as $objectName => $objectProperties) {
            $objectConfig = [
                'class'      => $this->entityClasses[$objectName],
                'namespace'  => $this->namespaces[$objectName],
                'path'       => $this->paths[$objectName],
                'properties' => $this->normalizeObjectProperties($objectProperties['properties']),
            ];

            $normalized['object'][$objectName] = $objectConfig;
        }

        foreach ($genConfig['transformers'] as $usage => $objects) {
            foreach ($objects as $objectName => $objectProperties) {
                $normalized[$usage][$objectName] = $normalized['object'][$objectName];
                $normalized[$usage][$objectName]['class'] = $objectProperties['class'];

                $properties = $objectProperties['properties'] ?? [];
                foreach ($properties as $name => $value) {
                    $normalized[$usage][$objectName]['properties'][$name] = $this->updateProperty($normalized[$usage][$objectName]['properties'][$name], $value);
                }
                $excludes = $objectProperties['exclude'] ?? [];
                foreach ($excludes as $exclude) {
                    unset($normalized[$usage][$objectName]['properties'][$exclude]);
                }
            }
        }

        return $normalized;
    }

    private function canonicalizePath($path)
    {
        // stolen from stackoverflow. yeah bitches, I own that shit!
        $path = explode('/', $path);
        $stack = array();
        foreach ($path as $seg) {
            if ($seg == '..') {
                // Ignore this segment, remove last segment from stack
                array_pop($stack);
                continue;
            }

            if ($seg == '.') {
                // Ignore this segment
                continue;
            }

            $stack[] = $seg;
        }

        return implode('/', $stack);
    }

    private function setConfig($config)
    {
        $this->objects = array_keys($config['objects']);
        if (empty($config['config']['transformations'])) {
            throw new InvalidArgumentException('The transformations is not found');
        }
        $this->transformations = $config['config']['transformations'];
        $this->usages = $config['config']['usage'] ?? [];

        $this->configureEntities($config);
        $this->configureTransformers($config);
    }

    private function configureEntities($config)
    {
        $this->entityConfigs = [];
        $entityConfig = $config['config']['entities'];
        $defaultNamespace = $entityConfig['_namespace'] ?? '';

        foreach ($this->objects as $object) {
            $className = $config['objects'][$object]['class'];
            $namespace = $entityConfig[$object]['_namespace'] ?? $defaultNamespace;
            $this->entityClasses[$object] = $namespace.'\\'.$className;
        }

    }

    private function configureTransformers($config)
    {
        $transformerConfig = $config['config']['transformers'];
        $defaultNamespace = $transformerConfig['_namespace'] ?? '';
        $defaultPath = $transformerConfig['_path'] ?? '';

        foreach ($this->objects as $object) {
            $namespace = $transformerConfig[$object]['_namespace'] ?? $defaultNamespace;
            $this->namespaces[$object] = $namespace;
            $path = $this->canonicalizePath($transformerConfig[$object]['_path'] ?? $defaultPath);
            $this->paths[$object] = $path;
        }
    }

    private function normalizeObjectProperties($properties = [])
    {
        $normalized = [];
        foreach ($properties as $name => $value) {
            $normalized[$name] = $this->normalizeObjectProperty($name, $value);
        }

        return $normalized;
    }

    private function normalizeObjectProperty(string $identifier, array $values)
    {
        foreach ($values as $key => $value) {
            if ( in_array($key, ['class', 'format', 'object', 'scalar',])) {
                $type = [$key => $value];
            }
        }

        return [
            'isCollection' => $values['isCollection'] ?? false,
            'name' => $values['name'] ?? $identifier,
            'type' => $type ?? ['scalar' => 'string'],
        ];
    }

    private function updateProperty(array $property, array $values)
    {
        $updated = $property;
        $type = [];
        foreach ($values as $key => $value) {
            if ( in_array($key, ['class', 'format', 'object', 'scalar', '_from', '_to'])) {
                $type[$key] = $value;
            }
        }
        $updated['type'] = !empty($type) ? $type : $property['type'];

        if (isset($values['name'])) {
            $updated['name'] = $values['name'];
        }

        return $updated;
    }
}
