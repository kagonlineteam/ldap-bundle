<?php

namespace KAGOnlineTeam\LdapBundle\Query\Filter;

/**
 * Abstract class which handles the filter type and the parent object for
 * fluent query building.
 *
 * @author Jan Flaßkamp
 */
abstract class AbstractFilter implements FilterInterface
{
    /**
     * An array of types from the FilterInterface which the filter implements.
     *
     * @var int[]
     */
    protected static $implementedTypes = [];

    private $type;
    private $parent;

    public function __construct(FilterInterface $parent, int $type)
    {
        if (!\in_array($type, static::$implementedTypes)) {
            throw new \InvalidArgumentException('The type is not supported by this class.');
        }

        $this->type = $type;

        if (method_exists($parent, 'addChild')) {
            $parent->addChild($this);
        }
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function end(): FilterInterface
    {
        return $this->parent;
    }
}
