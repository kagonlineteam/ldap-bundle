<?php

namespace KAGOnlineTeam\LdapBundle\Query\Filter;

/**
 * Basic interface for filters in the fluent query building.
 */
interface FilterInterface
{
    // Special type for the builder.
    const BUILDER = 0;

    // The typical Ldap filter types.
    const PRESENCE = 1;
    const EQUALITY = 2;
    const GREATER_OR_EQUAL = 3;
    const LESS_OR_EQUAL = 4;
    const SUBSTRING = 5;
    const APPROXIMATE = 6;
    const EXTENSIBLE = 7;
    const AND = 8;
    const OR = 9;
    const NOT = 10;

    // For filters whose types are unknown.
    const UNSPECIFIED = 255;

    /**
     * Returns the type of the filter.
     *
     * @return int The value from the type constants
     */
    public function getType(): int;

    /**
     * Allows parent objects to treat this filter as string. Each type has its own
     * string representation.
     *
     * @param callable $attrCall A callback which returns a attribute description
     * @param callable $escCall  A callback which escapes a filter value
     *
     * @return string The string representation of the filter
     */
    public function resolve(callable $attrCall, callable $escCall): string;

    /**
     * @return FilterInterface The parent filter object
     */
    public function end(): self;
}
