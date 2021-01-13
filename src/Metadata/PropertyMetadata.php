<?php

namespace KAGOnlineTeam\LdapBundle\Metadata;

/**
 * Stores the metadata for a single property which is associated with a Ldap
 * attribute.
 *
 * @author Jan Flaßkamp
 */
class PropertyMetadata
{
    private $property;
    private $attribute;

    public function __construct(string $property)
    {
        $this->property = $property;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function setAttribute(string $attribute): void
    {
        $this->attribute = $attribute;
    }
}
