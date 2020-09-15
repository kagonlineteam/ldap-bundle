<?php

namespace KAGOnlineTeam\LdapBundle;

use KAGOnlineTeam\LdapBundle\Exception\NoMetadataException;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;

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
     * @throws NoMetadataException      If metadata for the class cannot be found
     */
    public function getMetadata(string $class): ClassMetadataInterface;

    /**
     * Returns a repository instance for the given class.
     *
     * @param string $class The fully qualified class name
     *
     * @throws InvalidArgumentException If the given class does not exist
     * @throws NoMetadataException      If metadata for the class cannot be found
     */
    public function getRepository(string $class): RepositoryInterface;

    /**
     * Either marks the given object to be persisted or queues the
     * modifications made if the object has already been registered.
     */
    public function save(object $entry): void;

    /**
     * Queues the removal of entry from the Ldap server.
     */
    public function remove(object $entry): void;

    /**
     * All changes to entry object which have been marked via the save/remove
     * method will be committed to the Ldap server.
     */
    public function commit(): void;
}