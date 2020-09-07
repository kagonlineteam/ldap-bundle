<?php

namespace KAGOnlineTeam\LdapBundle;

use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;
use KAGOnlineTeam\LdapBundle\RepositoryInterface;
use KAGOnlineTeam\LdapBundle\Exception\NoMetadataException;

/**
 * Main service to obtain and manipulate entry objects.
 * 
 * @author Jan Flaßkamp
 */
interface EntryManagerInterface
{
    /**
     * Returns the class metadata for a given class.
     * 
     * @param string $class The fully qualified class name
     * 
     * @throws InvalidArgumentException If the given class does not exist
     * @throws NoMetadataException If metadata for the class cannot be found
     * 
     * @return ClassMetadataInterface
     */
    public function getMetadata(string $class): ClassMetadataInterface;

    /**
     * Returns a repository instance for the given class.
     * 
     * @param string $class The fully qualified class name
     * 
     * @throws InvalidArgumentException If the given class does not exist
     * @throws NoMetadataException If metadata for the class cannot be found
     * 
     * @return RepositoryInterface
     */
    public function getRepository(string $class): RepositoryInterface;

    /**
     * Either marks the given object to be persisted or queues the
     * modifications made if the object has already been registered.
     * 
     * @param object $entry
     * 
     * @return void
     */
    public function save(object $entry): void;

    /**
     * Queues the removal of entry from the Ldap server.
     * 
     * @param object $entry 
     * 
     * @return void
     */
    public function remove(object $entry): void;

    /**
     * All changes to entry object which have been marked via the save/remove 
     * method will be committed to the Ldap server.
     * 
     * @return void
     */
    public function commit(): void;
}