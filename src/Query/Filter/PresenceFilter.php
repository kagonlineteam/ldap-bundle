<?php

namespace KAGOnlineTeam\LdapBundle\Query\Filter;

/**
 * Implements the PRESENCE filter.
 *
 * @author Jan Flaßkamp
 */
class PresenceFilter extends AbstractFilter
{
    protected static $implementedTypes = [
        FilterInterface::PRESENCE,
    ];

    private $property;

    public function on(string $property): self
    {
        $this->property = $property;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(callable $attrCall, callable $escCall): string
    {
        return '('.$attrCall($this->property).'=*)';
    }
}
