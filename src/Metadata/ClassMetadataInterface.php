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
     * Returns the fully qualified class name of the associated class.
     */
    public function getClass(): string;
}
