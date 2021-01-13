<?php

namespace KAGOnlineTeam\LdapBundle\Tests\IntegrationTests;

use KAGOnlineTeam\LdapBundle\ManagerInterface;
use KAGOnlineTeam\LdapBundle\Metadata\DnMetadata;
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
        $this->assertEquals(new DnMetadata('dn'), $metadata->getDn());

        $usernameProperty = new PropertyMetadata('username');
        $usernameProperty->setAttribute('uid');
        $nameProperty = new PropertyMetadata('name');
        $nameProperty->setAttribute('givenName');
        $this->assertEquals([$usernameProperty, $nameProperty], $metadata->getProperties());
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
