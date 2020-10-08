<?php

namespace KAGOnlineTeam\LdapBundle\Query\Filter;

/**
 * Implements EQUALITY, GREATER OR EQUAL,
 * LESS OR EQUAL and APPROXIMATE LDAP filters.
 *
 * @author Jan Flaßkamp
 */
class FlatFilter extends AbstractFilter
{
    protected static $implementedTypes = [
        FilterInterface::EQUALITY,
        FilterInterface::GREATER_OR_EQUAL,
        FilterInterface::LESS_OR_EQUAL,
        FilterInterface::APPROXIMATE,
    ];

    private $property;
    private $value;

    public function with(string $property, string $value): self
    {
        $this->property = $property;
        $this->value = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(callable $attrCall, callable $escCall): string
    {
        $attribute = $attrCall($this->property);
        $value = $escCall($this->value);

        switch ($this->getType()) {
            case FilterInterface::EQUALITY:
                return '('.$attribute.'='.$value.')';
            case FilterInterface::GREATER_OR_EQUAL:
                return '('.$attribute.'>='.$value.')';
            case FilterInterface::LESS_OR_EQUAL:
                return '('.$attribute.'<='.$value.')';
            case FilterInterface::APPROXIMATE:
                return '('.$attribute.'~='.$value.')';
        }
    }
}
