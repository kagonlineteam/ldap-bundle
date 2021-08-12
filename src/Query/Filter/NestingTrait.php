<?php

namespace KAGOnlineTeam\LdapBundle\Query\Filter;

/**
 * A PHP trait to further nest LDAP queries.
 *
 * @author Jan Flaßkamp
 */
trait NestingTrait
{
    /**
     * Returns a FlatFilter instance of type "presence".
     *
     * @return FlatFilter The filter to configure
     */
    public function filterPresence(): PresenceFilter
    {
        return new PresenceFilter($this, FilterInterface::PRESENCE);
    }

    /**
     * Returns a FlatFilter instance of type "equality".
     *
     * @return FlatFilter The filter to configure
     */
    public function filterEquality(): FlatFilter
    {
        return new FlatFilter($this, FilterInterface::EQUALITY);
    }

    /**
     * Returns a FlatFilter instance of type "greater or equal".
     *
     * @return FlatFilter The filter to configure
     */
    public function filterGreaterOrEqual(): FlatFilter
    {
        return new FlatFilter($this, FilterInterface::GREATER_OR_EQUAL);
    }

    /**
     * Returns a FlatFilter instance of type "less or equal".
     *
     * @return FlatFilter The filter to configure
     */
    public function filterLessOrEqual(): FlatFilter
    {
        return new FlatFilter($this, FilterInterface::LESS_OR_EQUAL);
    }

    /**
     * Returns a FlatFilter instance of type "substring".
     *
     * @return FlatFilter The filter to configure
     */
    public function filterSubstring(): FlatFilter
    {
        return new FlatFilter($this, FilterInterface::SUBSTRING);
    }

    /**
     * Returns a FlatFilter instance of type "approximate".
     *
     * @return FlatFilter The filter to configure
     */
    public function filterApproximate(): FlatFilter
    {
        return new FlatFilter($this, FilterInterface::APPROXIMATE);
    }

    /**
     * Returns a NestedFilter instance of type "and".
     *
     * @return NestedFilter The filter to configure
     */
    public function filterAnd(): NestedFilter
    {
        return new NestedFilter($this, FilterInterface::AND);
    }

    /**
     * Returns a NestedFilter instance of type "or".
     *
     * @return NestedFilter The filter to configure
     */
    public function filterOr(): NestedFilter
    {
        return new NestedFilter($this, FilterInterface::OR);
    }

    /**
     * Returns a NotFilter instance of type "not".
     *
     * @return NotFilter The filter to configure
     */
    public function filterNot(): NestedFilter
    {
        return new NotFilter($this, FilterInterface::NOT);
    }
}
