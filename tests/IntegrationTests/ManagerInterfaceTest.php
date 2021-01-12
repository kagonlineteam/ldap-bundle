<?php

namespace KAGOnlineTeam\LdapBundle\Tests\IntegrationTests;

use KAGOnlineTeam\LdapBundle\ManagerInterface;
use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use KAGOnlineTeam\LdapBundle\Tests\LdapBundleKernelTestCase as KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ManagerInterfaceTest extends KernelTestCase
{
    public function testEntryManagerInterfaceIsAutowiredByContainer()
    {
        $builder = new ContainerBuilder();
        $builder->autowire(ManagerInterfaceAutowireTest::class)
            ->setPublic(true)
        ;

        self::bootKernel([
            'builder' => $builder,
        ]);

        $service = static::$container->get(ManagerInterfaceAutowireTest::class);

        $this->expectNotToPerformAssertions();
    }

    public function testGetMetadata()
    {
        self::bootKernel();

        $manager = static::$container->get('kagonlineteam_ldap.manager');
        $metadata = $manager->getMetadata(DummyUser::class);

        $this->assertSame(DummyUser::class, $metadata->getClass());
        $this->assertSame('KAGOnlineTeam\\LdapBundle\\Tests\\Fixtures\\DummyUserRepository', $metadata->getRepositoryClass());
        $this->assertSame(['inetOrgPerson', 'person', 'top'], $metadata->getObjectClasses());

        $dnProperty = new \ReflectionProperty(DummyUser::class, 'dn');
        $this->assertEquals($dnProperty, $metadata->getDnProperty());

        $usernamePropertyMetadata = new PropertyMetadata(
            new \ReflectionProperty(DummyUser::class, 'username'),
            'uid'
        );
        $namePropertyMetadata = new PropertyMetadata(
            new \ReflectionProperty(DummyUser::class, 'name'),
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

        $manager = static::$container->get('kagonlineteam_ldap.manager');

        $this->expectException(\InvalidArgumentException::class);
        $metadata = $manager->getMetadata('InvalidNamespace\\InvalidClass');
    }
}

class ManagerInterfaceAutowireTest
{
    public function __construct(ManagerInterface $manager)
    {
    }
}
