<?php

namespace KAGOnlineTeam\LdapBundle\Tests\IntegrationTests;

use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUserRepository;
use KAGOnlineTeam\LdapBundle\Tests\LdapBundleKernelTestCase as KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DummyUserRepositoryTest extends KernelTestCase
{
    public function testEntryManagerInterfaceIsAutowiredByContainer()
    {
        $builder = new ContainerBuilder();
        $builder->autowire(DummyUserRepository::class);
        $builder->autowire(DummyUserRepositoryAutowireTest::class)
            ->setPublic(true);

        self::bootKernel([
            'builder' => $builder,
        ]);

        $service = static::$container->get(DummyUserRepositoryAutowireTest::class);

        $this->expectNotToPerformAssertions();
    }
}

class DummyUserRepositoryAutowireTest
{
    public function __construct(DummyUserRepository $repository)
    {
    }
}
