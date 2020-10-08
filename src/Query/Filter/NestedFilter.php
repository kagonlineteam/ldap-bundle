<?php

namespace KAGOnlineTeam\LdapBundle\Query\Filter;

/**
 * Implements AND and OR LDAP filters.
 *
 * @author Jan Flaßkamp
 */
class NestedFilter extends AbstractFilter
{
    use NestingTrait;

    /**
     * {@inheritdoc}
     */
    protected static $implementedTypes = [
        FilterInterface::AND,
        FilterInterface::OR,
    ];

    /**
     * @var FilterInterface[]
     */
    private $children;

    /**
     * Adds a new child to this filter.
     *
     * @param object $child The new child
     */
    public function addChild(FilterInterface $child): void
    {
        $this->children[] = $child;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(callable $attrCall, callable $escCall): string
    {
        $children = '';
        foreach ($this->children as $child) {
            $children .= $child->resolve($attrCall, $escCall);
        }

        switch ($this->getType()) {
            case FilterInterface::AND:
                return '(&'.$children.')';
            case FilterInterface::OR:
                return '(|'.$children.')';
        }
    }
}
