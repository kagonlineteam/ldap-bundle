<?php

namespace KAGOnlineTeam\LdapBundle\Metadata;

/**
 * Stores the collected metadata for a class.
 *
 * @author Jan Flaßkamp
 */
class ClassMetadata
{
    private $class;
    private $repositoryClass;
    private $objectClasses = [];
    private $dn;
    private $properties = [];

    public function __construct(string $class)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('The class "%s" does not exist.', $class));
        }

        $this->class = $class;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getRepositoryClass(): string
    {
        return $this->repositoryClass;
    }

    public function setRepositoryClass(string $repositoryClass): void
    {
        $this->repositoryClass = $repositoryClass;
    }

    public function getObjectClasses(): array
    {
        return $this->objectClasses;
    }

    public function setObjectClasses(array $objectClasses): void
    {
        $this->objectClasses = array_unique($objectClasses);
    }

    public function getDn(): DnMetadata
    {
        return $this->dn;
    }

    public function setDn(DnMetadata $dn): void
    {
        $this->dn = $dn;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }
}
