<?php

namespace KAGOnlineTeam\LdapBundle\Metadata\Extractor;

use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;

/**
 * A metadata extractor extracts entry class definitions which have
 * been set by the user in different resources e.g. annotations.
 *
 * @author Jan Flaßkamp
 */
interface ExtractorInterface
{
    /**
     * Fills the given metadata with the extracted data.
     *
     * @throws NoMetadataException If the loader cannot find metadata for the class
     *
     * @param ClassMetadataInterface
     */
    public function extractFor(ClassMetadataInterface $metadata): void;
}
