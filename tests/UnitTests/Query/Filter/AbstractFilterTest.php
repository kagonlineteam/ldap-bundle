<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Query\Filter;

use KAGOnlineTeam\LdapBundle\Query\Filter\AbstractFilter;
use KAGOnlineTeam\LdapBundle\Query\Filter\FilterInterface;
use PHPUnit\Framework\TestCase;

class AbstractFilterTest extends TestCase
{
    public function testConstruct(): void
    {
        $double = $this->prophesize(FilterInterface::class);
        $double = $double->reveal();

        $parent = new AbstractFilterTestClass($double, 0);
        $filter = new AbstractFilterTestClass($parent, 89);
        $this->assertSame(0, $parent->getType());
        $this->assertSame(89, $filter->getType());
        $this->assertSame($parent, $filter->end());
        $this->assertSame($filter, $parent->getChild());
        $this->assertSame($double, $parent->end());

        $this->expectException(\InvalidArgumentException::class);
        $filter2 = new AbstractFilterTestClass($parent, 1);
    }
}

class AbstractFilterTestClass extends AbstractFilter
{
    protected static $implementedTypes = [
        0, 89,
    ];

    public function resolve(callable $attrCall, callable $escCall): string
    {
        return '';
    }

    private $child;

    public function addChild(FilterInterface $child): void
    {
        $this->child = $child;
    }

    public function getChild()
    {
        return $this->child;
    }
}
