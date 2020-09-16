<?php

namespace KAGOnlineTeam\LdapBundle\Tests\IntegrationTests;

use InvalidArgumentException;
use KAGOnlineTeam\LdapBundle\EntryManagerInterface;
use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use KAGOnlineTeam\LdapBundle\Tests\LdapBundleKernelTestCase as KernelTestCase;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EntryManagerInterfaceTest extends KernelTestCase
{
    public function testEntryManagerInterfaceIsAutowiredByContainer()
    {
        $builder = new ContainerBuilder();
        $builder->autowire(EntryManagerInterfaceAutowireTest::class)
            ->setPublic(true)
        ;

        self::bootKernel([
            'builder' => $builder,
        ]);

        $service = static::$container->get(EntryManagerInterfaceAutowireTest::class);

        $this->expectNotToPerformAssertions();
    }

    public function testGetMetadata()
    {
        self::bootKernel();

        $manager = static::$container->get(EntryManagerInterface::class);
        $metadata = $manager->getMetadata(DummyUser::class);

        $this->assertSame(DummyUser::class, $metadata->getClass());
        $this->assertSame('KAGOnlineTeam\\LdapBundle\\Tests\\Fixtures\\DummyUserRepository', $metadata->getRepositoryClass());
        $this->assertSame(['inetOrgPerson', 'person', 'top'], $metadata->getObjectClasses());

        $dnProperty = new ReflectionProperty(DummyUser::class, 'dn');
        $this->assertEquals($dnProperty, $metadata->getDnProperty());

        $usernamePropertyMetadata = new PropertyMetadata(
            new ReflectionProperty(DummyUser::class, 'username'),
            'uid'
        );
        $namePropertyMetadata = new PropertyMetadata(
            new ReflectionProperty(DummyUser::class, 'name'),
            'givenName'
        );
        foreach ([$usernamePropertyMetadata, $namePropertyMetadata] as $propertyMetadata) {
            $this->assertTrue($metadata->hasProperty($propertyMetadata->getName()));
            $this->assertEquals($propertyMetadata, $metadata->getProperty($propertyMetadata->getName()));
        }
    }

    public function testGetMetadataWithInvalidClass()
    {
        self::bootKernel();

        $manager = static::$container->get(EntryManagerInterface::class);

        $this->expectException(InvalidArgumentException::class);
        $metadata = $manager->getMetadata('InvalidNamespace\\InvalidClass');
    }
}

class EntryManagerInterfaceAutowireTest
{
    public function __construct(EntryManagerInterface $em)
    {
    }
}
