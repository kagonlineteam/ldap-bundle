<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Query;

use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;
use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;
use KAGOnlineTeam\LdapBundle\Query\Builder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

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
        $propMeta1->getName()->willReturn('exProperty')->shouldBeCalledTimes(1);
        $propMeta1->getAttribute()->willReturn('exAttribute')->shouldBeCalledTimes(1);

        $propMeta2 = $this->prophesize(PropertyMetadata::class);
        $propMeta2->getName()->willReturn('name')->shouldBeCalledTimes(1);
        $propMeta2->getAttribute()->willReturn('givenName')->shouldBeCalledTimes(1);

        $metadata = $this->prophesize(ClassMetadataInterface::class);
        $metadata->getClass()->willReturn('SomeNamespace\\SomeClass');
        $metadata->getProperties(Argument::any())->willReturn([$propMeta1->reveal(), $propMeta2->reveal()])->shouldBeCalledTimes(1);

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
