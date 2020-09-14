<?php

namespace KAGOnlineTeam\LdapBundle\Metadata;

use ReflectionClass;
use ReflectionProperty;
use InvalidArgumentException;
use ReflectionException;
use function array_search;
use function array_unique;
use function key_exists;

/**
 * 
 *
 * @author Jan Flaßkamp
 */
class ClassMetadata implements ClassMetadataInterface
{
    /**
     * The reflection class of the associated class.
     *
     * @var ReflectionClass
     */
    private $reflection;

    /**
     * @var string The class of the repository
     */
    private $repositoryClass;

    /**
     * @var array An array which contains all necessary objectClasses
     */
    private $objectClasses = [];

    /**
     * @var ReflectionProperty
     */
    private $dnProperty;

    /**
     * @var PropertyMetadata[]
     */
    private $properties = [];

    public function __construct(string $class)
    {
        try {
            $this->reflection = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    public function getClass(): string
    {
        return $this->reflection->getName();
    }

    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflection;
    }

    public function getRepositoryClass(): string
    {
        return $this->repositoryClass;
    }

    public function setRepositoryClass(string $repositoryClass): self
    {
        $this->repositoryClass = $repositoryClass;

        return $this;
    }

    public function getObjectClasses(): array
    {
        return $this->objectClasses;
    }

    public function addObjectClass(string $objectClass): self
    {
        $result = array_search($objectClass, $this->objectClasses, true);

        if (false !== $result) {
            throw new InvalidArgumentException("The objectClass has already been added.");
        }

        $this->objectClasses[] = $objectClass;

        return $this;
    }

    public function setObjectClasses(array $objectClasses): self
    {
        $this->objectClasses = array_unique($objectClasses);

        return $this;
    }

    public function getDnProperty(): ReflectionProperty
    {
        return $this->dnProperty;
    }

    public function setDnProperty(ReflectionProperty $dnProperty): self
    {
        $this->dnProperty = $dnProperty;

        return $this;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): self
    {
        $keyedProperties = [];
        foreach ($properties as $property) {
            if (!$property instanceof PropertyMetadata) {
                throw new InvalidArgumentException(sprintf("The metadata for a property must be of type \"%s\"", PropertyMetadata::class));
            }
            $keyedProperties[$property->getName()] = $property;
        }

        $this->properties = $keyedProperties;

        return $this;
    }

    public function hasProperty(string $name): bool
    {
        return key_exists($name, $this->properties);
    }

    public function getProperty(string $name): PropertyMetadata
    {
        if (!$this->hasProperty($name)) {
            throw new InvalidArgumentException(sprintf("The metadata for \"%s\" does not exist.", $name));
        }

        return $this->properties[$name];
    }

    public function addProperty(PropertyMetadata $property): self
    {
        $name = $property->getName();
        if ($this->hasProperty($name)) {
            throw new InvalidArgumentException(sprintf("The metadata for \"%s\" has already been added.", $name));
        }
        
        $this->properties[$name] = $property;

        return $this;
    }

    public function replaceProperty(PropertyMetadata $property): self
    {
        $name = $property->getName();
        if (!$this->hasProperty($name)) {
            throw new InvalidArgumentException(sprintf("The metadata for \"%s\" does not exist.", $name));
        }

        $this->properties[$name] = $property;

        return $this;
    }

    public function removeProperty(PropertyMetadata $property): self
    {
        $name = $property->getName();
        if (!$this->hasProperty($name)) {
            throw new InvalidArgumentException(sprintf("The metadata for \"%s\" does not exist.", $name));
        }

        unset($this->properties[$name]);

        return $this;
    }
}