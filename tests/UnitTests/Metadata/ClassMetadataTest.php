<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Metadata;

use InvalidArgumentException;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;
use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUserRepository;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;

class ClassMetadataTest extends TestCase
{
    public function testInstanceOfClassMetadataInterface()
    {
        $metadata = new ClassMetadata(DummyUser::class);

        $this->assertInstanceOf(ClassMetadataInterface::class, $metadata);
    }

    public function testValues()
    {
        $metadata = new ClassMetadata(DummyUser::class);
        $metadata->setRepositoryClass(DummyUserRepository::class);
        $metadata->setObjectClasses(['top']);
        $metadata->addObjectClass('person');

        $dnProperty = new ReflectionProperty(DummyUser::class, 'dn');
        $metadata->setDnProperty($dnProperty);

        $this->assertSame(DummyUser::class, $metadata->getClass());
        $this->assertEquals(new ReflectionClass(DummyUser::class), $metadata->getReflectionClass());
        $this->assertSame(DummyUserRepository::class, $metadata->getRepositoryClass());
        $this->assertSame(['top', 'person'], $metadata->getObjectClasses());
        $this->assertSame($dnProperty, $metadata->getDnProperty());

        $usernameProperty = $this->prophesize(PropertyMetadata::class);
        $usernameProperty->getName()->willReturn('username');
        $usernameProperty = $usernameProperty->reveal();

        $nameProperty = $this->prophesize(PropertyMetadata::class);
        $nameProperty->getName()->willReturn('name');
        $nameProperty = $nameProperty->reveal();

        $metadata->setProperties([$usernameProperty, $nameProperty]);
        $this->assertSame([
            'username' => $usernameProperty,
            'name' => $nameProperty,
        ], $metadata->getProperties());

        $this->assertTrue($metadata->hasProperty('name'));
        $this->assertFalse($metadata->hasProperty('unknown property'));

        $this->assertSame($usernameProperty, $metadata->getProperty('username'));

        $metadata->removeProperty($nameProperty);
        $this->assertSame([
            'username' => $usernameProperty,
        ], $metadata->getProperties());
        $this->assertFalse($metadata->hasProperty('name'));

        $usernameProperty2 = $this->prophesize(PropertyMetadata::class);
        $usernameProperty2->getName()->willReturn('username');
        $usernameProperty2 = $usernameProperty2->reveal();

        $metadata->replaceProperty($usernameProperty2);
        $this->assertSame($usernameProperty2, $metadata->getProperty('username'));

        $metadata->addProperty($nameProperty);
        $this->assertSame([
            'username' => $usernameProperty2,
            'name' => $nameProperty,
        ], $metadata->getProperties());
    }

    /*public function testFluency()
    {
        $metadata = new ClassMetadata(DummyUser::class);

        $res = $metadata->setRepositoryClass(DummyUserRepository::class);
        $this->assertSame($metadata, $res);

        $res = $metadata->setObjectClasses(['top']);
        $this->assertSame($metadata, $res);

        $res = $metadata->addObjectClass('person');
        $this->assertSame($metadata, $res);

        $res = $metadata->setDnProperty((new ReflectionProperty(DummyUser::class, 'username')));
        $this->assertSame($metadata, $res);

        $usernameProperty = $this->prophesize(PropertyMetadata::class);
        $usernameProperty->getName()->willReturn('username');
        $usernameProperty = $usernameProperty->reveal();

        $nameProperty = $this->prophesize(PropertyMetadata::class);
        $nameProperty->getName()->willReturn('name');
        $nameProperty = $nameProperty->reveal();

        $res = $metadata->setProperties([$usernameProperty, $nameProperty]);
        $this->assertSame($metadata, $res);

        $res = $metadata->removeProperty($nameProperty);
        $this->assertSame($metadata, $res);

        $res = $metadata->replaceProperty($usernameProperty);
        $this->assertSame($metadata, $res);

        $res = $metadata->addProperty($nameProperty);
        $this->assertSame($metadata, $res);
    }*/

    public function testConstructorException()
    {
        $this->expectException(InvalidArgumentException::class);
        $metadata = new ClassMetadata('InvalidNamespace\\InvalidUserClass');
    }

    public function testAddObjectClassException()
    {
        $this->expectException(InvalidArgumentException::class);
        $metadata = new ClassMetadata(DummyUser::class);
        $metadata->setObjectClasses(['top', 'person']);

        $metadata->addObjectClass('person');
    }

    public function testPropertiesWithInvalidTypeExcepion()
    {
        $usernameProperty = $this->prophesize(PropertyMetadata::class);
        $usernameProperty->getName()->willReturn('username');
        $usernameProperty = $usernameProperty->reveal();

        $this->expectException(InvalidArgumentException::class);

        $metadata = (new ClassMetadata(DummyUser::class))
            ->setProperties([$usernameProperty, 2]);
    }

    public function testDoublePropertyExcepion()
    {
        $usernameProperty = $this->prophesize(PropertyMetadata::class);
        $usernameProperty->getName()->willReturn('username');
        $usernameProperty = $usernameProperty->reveal();

        $this->expectException(InvalidArgumentException::class);

        $metadata = (new ClassMetadata(DummyUser::class))
            ->addProperty($usernameProperty)
            ->addProperty($usernameProperty);
    }

    public function testUnknownPropertyException()
    {
        $this->expectException(InvalidArgumentException::class);

        $metadata = (new ClassMetadata(DummyUser::class))
            ->getProperty('unknown');
    }
}
