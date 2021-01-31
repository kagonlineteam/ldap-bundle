<?php

namespace KAGOnlineTeam\LdapBundle\Query\Filter;

class NotFilter extends AbstractFilter
{
    use NestingTrait;

    /**
     * {@inheritdoc}
     */
    protected static $implementedTypes = [
        FilterInterface::NOT,
    ];

    private $child;

    /**
     * Adds a new child to this filter.
     *
     * @param object $child The new child
     */
    public function addChild(FilterInterface $child): void
    {
        $this->child = $child;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(callable $attrCall, callable $escCall): string
    {
        if (!isset($this->child)) {
            throw new \LogicException('No filter has been set.');
        }

        return '(!'.$this->child->resolve($attrCall, $escCall).')';
    }
}
