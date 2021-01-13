<?php

namespace KAGOnlineTeam\LdapBundle\Metadata\Factory;

use KAGOnlineTeam\LdapBundle\Exception\NoMetadataException;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;

/**
 * Interface for all different metadata factories.
 *
 * @author Jan Flaßkamp
 */
interface MetadataFactoryInterface
{
    /**
     * Creates the class metadata for a given class.
     *
     * @param string $class The fully qualified class name
     *
     * @throws \InvalidArgumentException If the class does not exist
     * @throws NoMetadataException       If no metadata can be created
     */
    public function create(string $class): ClassMetadata;
}
