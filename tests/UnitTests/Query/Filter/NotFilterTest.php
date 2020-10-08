<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Query\Filter;

use KAGOnlineTeam\LdapBundle\Query\Filter\FilterInterface;
use KAGOnlineTeam\LdapBundle\Query\Filter\NotFilter;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class NotFilterTest extends TestCase
{
    public function testResolve()
    {
        $parent = $this->prophesize(FilterInterface::class);
        $parentDouble = $parent->reveal();

        $filter = new NotFilter($parentDouble, FilterInterface::NOT);

        $child1 = $this->prophesize(FilterInterface::class);
        $child1->resolve(Argument::type('callable'), Argument::type('callable'))->willReturn('(objectClass=top)');
        $filter->addChild($child1->reveal());

        $this->assertSame($parentDouble, $filter->end());
        $this->assertSame('(!(objectClass=top))', $filter->resolve([$this, 'attributeCallback'], [self::class, 'escapeCallback']));
    }

    public function attributeCallback(string $property): string
    {
        return $property;
    }

    public static function escapeCallback(string $value): string
    {
        return 'escaped:"'.$value.'"';
    }
}
