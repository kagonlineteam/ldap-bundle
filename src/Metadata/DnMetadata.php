<?php

namespace KAGOnlineTeam\LdapBundle\Metadata;

/**
 * @author Jan Flaßkamp
 */
class DnMetadata
{
    private $property;

    public function __construct(string $property)
    {
        $this->property = $property;
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}
