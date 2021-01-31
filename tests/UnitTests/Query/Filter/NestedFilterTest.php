<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Query\Filter;

use KAGOnlineTeam\LdapBundle\Query\Filter\FilterInterface;
use KAGOnlineTeam\LdapBundle\Query\Filter\NestedFilter;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class NestedFilterTest extends TestCase
{
    /**
     * @dataProvider provideResolve
     */
    public function testResolve(int $type, array $children, string $expectedResult)
    {
        $parent = $this->prophesize(FilterInterface::class);
        $parentDouble = $parent->reveal();

        $filter = new NestedFilter($parentDouble, $type);

        foreach ($children as $child) {
            $filter->addChild($child);
        }

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
        $child1 = $this->prophesize(FilterInterface::class);
        $child1->resolve(Argument::type('callable'), Argument::type('callable'))->willReturn('(objectClass=top)');
        $child2 = $this->prophesize(FilterInterface::class);
        $child2->resolve(Argument::type('callable'), Argument::type('callable'))->willReturn('(sn=AA)');

        yield [FilterInterface::AND, [
            $child1->reveal(),
            $child2->reveal(),
        ], '(&(objectClass=top)(sn=AA))'];

        yield [FilterInterface::OR, [
            $child1->reveal(),
            $child2->reveal(),
        ], '(|(objectClass=top)(sn=AA))'];
    }
}
