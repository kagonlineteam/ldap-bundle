<?php

namespace KAGOnlineTeam\LdapBundle\Query\Filter;

/**
 * Adapter for filters built outside of the query builder. The filter will NOT
 * be escaped.
 */
class RawFilter extends AbstractFilter
{
    protected static $implementedTypes = [
        FilterInterface::UNSPECIFIED,
    ];

    /**
     * @var string The raw filter
     */
    private $filter;

    public function from(string $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(callable $attrCall, callable $escCall): string
    {
        return $this->filter;
    }
}
