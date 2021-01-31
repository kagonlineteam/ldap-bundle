<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Query;

use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;
use KAGOnlineTeam\LdapBundle\Query\Builder;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    /**
     * @dataProvider provideEscape
     */
    public function testEscape(string $value, string $result)
    {
        $this->assertSame($result, Builder::escape($value));
    }

    public function testGetAttribute()
    {
        $propMeta1 = $this->prophesize(PropertyMetadata::class);
        $propMeta1->getProperty()->willReturn('exProperty');
        $propMeta1->getAttribute()->willReturn('exAttribute');

        $propMeta2 = $this->prophesize(PropertyMetadata::class);
        $propMeta2->getProperty()->willReturn('name');
        $propMeta2->getAttribute()->willReturn('givenName');

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getClass()->willReturn('SomeNamespace\\SomeClass');
        $metadata->getProperties()->willReturn([$propMeta1->reveal(), $propMeta2->reveal()]);

        $builder = new Builder('', $metadata->reveal());
        $this->assertSame('exAttribute', $builder->getAttribute('exProperty'));
        $this->assertSame('givenName', $builder->getAttribute('name'));
        $builder->resolveProperties(false);
        $this->assertSame('exProperty', $builder->getAttribute('exProperty'));
    }

    public function provideEscape(): \Generator
    {
        yield ['E\\m*', 'E\\5cm\\2a'];
        yield ['', '\\00'];
        yield ['(WBB', '\\28WBB'];
    }
}
