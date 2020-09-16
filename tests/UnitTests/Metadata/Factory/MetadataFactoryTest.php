<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Metadata\Factory;

use InvalidArgumentException;
use KAGOnlineTeam\LdapBundle\Exception\NoMetadataException;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;
use KAGOnlineTeam\LdapBundle\Metadata\Extractor\ExtractorInterface;
use KAGOnlineTeam\LdapBundle\Metadata\Factory\MetadataFactory;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class MetadataFactoryTest extends TestCase
{
    public function testFound()
    {
        $extractor1 = $this->prophesize(ExtractorInterface::class);
        $extractor1->extractFor(Argument::type(ClassMetadataInterface::class))->willThrow(new NoMetadataException())->shouldBeCalled();

        $extractor2 = $this->prophesize(ExtractorInterface::class);
        $extractor2->extractFor(Argument::type(ClassMetadataInterface::class))->will(function ($args) {
            $args[0]->setObjectClasses(['inetOrgPerson']);
        })->shouldBeCalled();

        $factory = new MetadataFactory([$extractor1->reveal(), $extractor2->reveal()]);
        $metadata = $factory->create(DummyUser::class);

        $this->assertSame(['inetOrgPerson'], $metadata->getObjectClasses());
    }

    public function testNotFound()
    {
        $this->expectException(NoMetadataException::class);

        $extractor = $this->prophesize(ExtractorInterface::class);
        $extractor->extractFor(Argument::which('getClass', DummyUser::class))->willThrow(new NoMetadataException())->shouldBeCalled();

        $factory = new MetadataFactory([$extractor->reveal(), $extractor->reveal()]);
        $factory->create(DummyUser::class);
    }

    public function testInvalidClass()
    {
        $this->expectException(InvalidArgumentException::class);

        $factory = new MetadataFactory([]);
        $factory->create('InvalidNamespace\\InvalidClass');
    }
}
