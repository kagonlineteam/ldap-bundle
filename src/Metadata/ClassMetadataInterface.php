<?php

namespace KAGOnlineTeam\LdapBundle\Metadata;

/**
 * Stores the collected metadata for a class.
 *
 * @author Jan Flaßkamp
 */
interface ClassMetadataInterface
{
    /**
     * @return string The fully qualified class name of the associated class.
     */
    public function getClass(): string;

    /**
     * @return string The fully qualified class name of the repository for the associated class.
     */
    public function getRepositoryClass(): string;

    /**
     * Defines the "ObjectClass" values which will be given to new entries and 
     * will be required from existing entries to instanciate new objects. 
     * 
     * @return string[] A list of valid ObjectClass values
     */
    public function getObjectClasses(): array;

    /**
     * 
     */
    //public function getDistinguishedName(): array;

    /**
     * Sets a new repository class for the associated class.
     *
     * @param string $repositoryClass The fully qualified class name of the repository.
     */
    public function setRepositoryClass(string $repositoryClass): void;

    /**
     * @param string[] $objectClasses The new "ObjectClass" values
     */
    public function setObjectClasses(array $objectClasses): void;

    /**
     * 
     */
    //public function setDistinguishedName(string $property): void;

    public function getProperties();
    public function getDnProperty();
    public function getReflectionClass();
}
