<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Query\Filter;

use KAGOnlineTeam\LdapBundle\Query\Filter\FilterInterface;
use KAGOnlineTeam\LdapBundle\Query\Filter\PresenceFilter;
use PHPUnit\Framework\TestCase;

class PresenceFilterTest extends TestCase
{
    public function testResolve()
    {
        $parent = $this->prophesize(FilterInterface::class);
        $parentDouble = $parent->reveal();

        $filter = new PresenceFilter($parentDouble, FilterInterface::PRESENCE);
        $filter->on('name');

        $this->assertSame($parentDouble, $filter->end());
        $this->assertSame('(givenName=*)', $filter->resolve([$this, 'attributeCallback'], [self::class, 'escapeCallback']));
    }

    public function attributeCallback(string $property): string
    {
        if ('name' === $property) {
            return 'givenName';
        }

        return $property;
    }

    public static function escapeCallback(string $value): string
    {
        return 'escaped:"'.$value.'"';
    }
}
