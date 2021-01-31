<?php

namespace KAGOnlineTeam\LdapBundle\Metadata;

/**
 * @author Jan Flaßkamp
 */
class DnMetadata
{
    const TYPE_STRING = 'string';
    const TYPE_OBJECT = 'object';

    private $property;
    private $type;

    public function __construct(string $property, string $type)
    {
        $this->property = $property;

        if (!\in_array($type, [self::TYPE_STRING, self::TYPE_OBJECT])) {
            throw new \InvalidArgumentException('Unknown DN type given.');
        }
        $this->type = $type;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
