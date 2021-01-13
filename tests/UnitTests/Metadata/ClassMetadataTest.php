<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Metadata;

use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use KAGOnlineTeam\LdapBundle\Metadata\DnMetadata;
use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUserRepository;
use PHPUnit\Framework\TestCase;

class ClassMetadataTest extends TestCase
{
    public function testValues()
    {
        $dnMetadata = $this->prophesize(DnMetadata::class);
        $dnMetadata->getProperty()->willReturn('dn');
        $dnMetadata = $dnMetadata->reveal();
        $usernameProperty = $this->prophesize(PropertyMetadata::class);
        $usernameProperty->getProperty()->willReturn('username');
        $usernameProperty = $usernameProperty->reveal();
        $nameProperty = $this->prophesize(PropertyMetadata::class);
        $nameProperty->getProperty()->willReturn('name');
        $nameProperty = $nameProperty->reveal();

        $metadata = new ClassMetadata(DummyUser::class);
        $metadata->setRepositoryClass(DummyUserRepository::class);
        $metadata->setObjectClasses(['top', 'person']);
        $metadata->setDn($dnMetadata);
        $metadata->setProperties([$usernameProperty, $nameProperty]);

        $this->assertSame(DummyUser::class, $metadata->getClass());
        $this->assertSame(DummyUserRepository::class, $metadata->getRepositoryClass());
        $this->assertSame(['top', 'person'], $metadata->getObjectClasses());
        $this->assertSame($dnMetadata, $metadata->getDn());
        $this->assertSame([$usernameProperty, $nameProperty], $metadata->getProperties());
    }

    public function testConstructorException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $metadata = new ClassMetadata('InvalidNamespace\\InvalidUserClass');
    }
}
