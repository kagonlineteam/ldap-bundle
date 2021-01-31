<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Query\Filter;

use KAGOnlineTeam\LdapBundle\Query\Filter\FilterInterface;
use KAGOnlineTeam\LdapBundle\Query\Filter\FlatFilter;
use PHPUnit\Framework\TestCase;

class FlatFilterTest extends TestCase
{
    /**
     * @dataProvider provideResolve
     */
    public function testResolve(int $type, string $property, string $value, string $expectedResult)
    {
        $parent = $this->prophesize(FilterInterface::class);
        $parentDouble = $parent->reveal();

        $filter = new FlatFilter($parentDouble, $type);
        $filter->with($property, $value);

        $this->assertSame($parentDouble, $filter->end());
        $this->assertSame($expectedResult, $filter->resolve([$this, 'attributeCallback'], [self::class, 'escapeCallback']));
    }

    public function attributeCallback(string $property): string
    {
        return $property;
    }

    public static function escapeCallback(string $value): string
    {
        return 'escaped:"'.$value.'"';
    }

    public function provideResolve(): \Generator
    {
        yield [FilterInterface::EQUALITY, 'objectClass', 'inetOrgPerson', '(objectClass=escaped:"inetOrgPerson")'];
        yield [FilterInterface::GREATER_OR_EQUAL, 'employeeNumber', (string) 2, '(employeeNumber>=escaped:"2")'];
        yield [FilterInterface::LESS_OR_EQUAL, 'employeeNumber', (string) 4, '(employeeNumber<=escaped:"4")'];
    }
}
