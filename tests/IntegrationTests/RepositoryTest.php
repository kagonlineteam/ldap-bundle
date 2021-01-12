<?php

namespace KAGOnlineTeam\LdapBundle\Tests\IntegrationTests;

use KAGOnlineTeam\LdapBundle\Connection\ConnectionInterface;
use KAGOnlineTeam\LdapBundle\Request;
use KAGOnlineTeam\LdapBundle\Response;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUserRepository;
use KAGOnlineTeam\LdapBundle\Tests\LdapBundleKernelTestCase as KernelTestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Prophecy\Argument;

class RepositoryTest extends KernelTestCase
{
    public function testRepositoryAutowiring()
    {
        $builder = new ContainerBuilder();
        $builder->autowire(DummyUserRepository::class);
        $builder->autowire(RepositoryAutowireTest::class)
            ->setPublic(true);

        self::bootKernel([
            'builder' => $builder,
        ]);

        $service = static::$container->get(RepositoryAutowireTest::class);

        $this->expectNotToPerformAssertions();
    }

    public function testRepositoryQuerying()
    {
        $findRequest = new Request\QueryRequest('uid=SomeUser,ou=users,ou=system', '(&(objectClass=inetOrgPerson)(objectClass=person)(objectClass=top))', [], false);
        $findResponse = new Response\EntriesResponse([
            ['uid=SomeUser,ou=users,ou=system', [
                'top', 'person', 'inetOrgPerson',
                ], [
                    'uid' => ['SomeUser'],
                    'givenName' => ['Max'],
                ],
            ],
        ], false);

        $updateRequest = new Request\UpdateRequest('uid=SomeUser,ou=users,ou=system', [
            'dn' => 'uid=OtherUser,ou=users,ou=system',
            'attributes' => [
                'uid' => [
                    'add' => ['OtherUser'],
                    'keep' => [],
                    'delete' => ['SomeUser'],
                ],
                'givenName' => [
                    'add' => [],
                    'keep' => ['Max'],
                    'delete' => []
                ]
            ]
        ]);
        $updateResponse = new Response\SuccessResponse();

        $connection = $this->prophesize(ConnectionInterface::class);
        $connection->getBaseDn()->willReturn('ou=users,ou=system');
        $connection->execute(Argument::exact($findRequest))->willReturn($findResponse)->shouldBeCalledTimes(1);
        $connection->execute(Argument::exact($updateRequest))->willReturn($updateResponse)->shouldBeCalledTimes(1);
        ConnCallback::$conn = $connection->reveal();

        $builder = new ContainerBuilder();
        $builder->autowire(DummyUserRepository::class)
            ->setPublic(true);

        self::bootKernel([
            'builder' => $builder,
            'compiler_passes' => [new ConnectionCompilerPass([ConnCallback::class, 'getConn'])],
        ]);

        $repository = static::$container->get(DummyUserRepository::class);

        $actualUser = $repository->find('uid=SomeUser,ou=users,ou=system');
        $expectedUser = new DummyUser('uid=SomeUser,ou=users,ou=system', ['SomeUser'], ['Max']);
        $this->assertEquals($expectedUser, $actualUser);

        $actualUser->setUsername('OtherUser');
        $repository->commit();
    }
}

class ConnCallback
{
    public static $conn;
    public static function getConn()
    {
        return static::$conn;
    }
}

class ConnectionCompilerPass implements CompilerPassInterface
{
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('kagonlineteam_ldap.connection_factory');
        $definition->replaceArgument(0, $this->connection);
    }
}

class RepositoryAutowireTest
{
    public function __construct(DummyUserRepository $repository)
    {
    }
}

