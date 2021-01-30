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
    const TYPE_SCALAR = 'scalar';
    const TYPE_ARRAY = 'array';
    const TYPE_MULIVALUE = 'multivalue';

    private $property;
    private $attribute;
    private $type;

    public function __construct(string $property, string $attribute, string $type)
    {
        $this->property = $property;
        $this->attribute = $attribute;

        if (!\in_array($type, [self::TYPE_SCALAR, self::TYPE_ARRAY, self::TYPE_MULIVALUE])) {
            throw new \InvalidArgumentException('Unknown DN type given.');
        }
        $this->type = $type;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
