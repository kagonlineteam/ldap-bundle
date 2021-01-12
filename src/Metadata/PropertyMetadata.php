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
    /**
     * @var ReflectionProperty
     */
    private $reflection;

    /**
     * @var string
     */
    private $attribute;

    public function __construct(\ReflectionProperty $reflection, string $attribute)
    {
        $this->reflection = $reflection;
        $this->attribute = $attribute;
    }

    /**
     * @var string The name of the property
     */
    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getReflectionProperty(): \ReflectionProperty
    {
        return $this->reflection;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }
}
