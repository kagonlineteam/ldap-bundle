<?php

namespace KAGOnlineTeam\LdapBundle\Tests\IntegrationTests;

use KAGOnlineTeam\LdapBundle\Connection\ConnectionInterface;
use KAGOnlineTeam\LdapBundle\Request;
use KAGOnlineTeam\LdapBundle\Response;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUserRepository;
use KAGOnlineTeam\LdapBundle\Tests\LdapBundleKernelTestCase as KernelTestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
        $findRequest = new Request\QueryRequest('uid=SomeUser,ou=users,ou=system', '(&(objectClass=inetOrgPerson)(objectClass=person)(objectClass=top))', ['scope' => 'base'], false);
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
            'objectClass' => ['inetOrgPerson', 'person', 'top'],
            'attributes' => [
                'uid' => [
                    'add' => ['OtherUser'],
                    'keep' => [],
                    'delete' => ['SomeUser'],
                ],
                'givenName' => [
                    'add' => [],
                    'keep' => ['Max'],
                    'delete' => [],
                ],
            ],
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

    public function testRollback()
    {
        $queryRequest = new Request\QueryRequest('ou=users,ou=system', '(&(objectClass=inetOrgPerson)(objectClass=person)(objectClass=top)(givenName=John))', ['scope' => 'sub'], false);
        $queryResponse = new Response\EntriesResponse([
            ['uid=John12,ou=users,ou=system', [
                'top', 'person', 'inetOrgPerson',
                ], [
                    'uid' => ['John12'],
                    'givenName' => ['John'],
                ],
            ],
            ['uid=JJason,ou=users,ou=system', [
                'top', 'person', 'inetOrgPerson',
                ], [
                    'uid' => ['JJason'],
                    'givenName' => ['John', 'Jason'],
                ],
            ],
            ['uid=Jn0,ou=users,ou=system', [
                'top', 'person', 'inetOrgPerson',
                ], [
                    'uid' => ['Jn0'],
                    'givenName' => ['John'],
                ],
            ],
            ['uid=John,ou=users,ou=system', [
                'top', 'person', 'inetOrgPerson',
                ], [
                    'uid' => ['John'],
                    'givenName' => ['John'],
                ],
            ],
            ['uid=John00,ou=admin,ou=users,ou=system', [
                'top', 'person', 'inetOrgPerson',
                ], [
                    'uid' => ['John00'],
                    'givenName' => ['John'],
                ],
            ],
        ], false);

        $successResponse = new Response\SuccessResponse();
        $failureResponse = new Response\FailureResponse('Some error occurred.');
        $updateRequest0 = new Request\UpdateRequest('uid=Jn0,ou=users,ou=system', [
            'dn' => 'uid=J0hn,ou=users,ou=system',
            'objectClass' => ['inetOrgPerson', 'person', 'top'],
            'attributes' => [
                'uid' => [
                    'add' => ['J0hn'],
                    'keep' => [],
                    'delete' => ['Jn0'],
                ],
                'givenName' => [
                    'add' => [],
                    'keep' => ['John'],
                    'delete' => [],
                ],
            ],
        ]);
        $updateRequest1 = new Request\UpdateRequest('uid=John12,ou=users,ou=system', [
            'dn' => null,
            'objectClass' => ['inetOrgPerson', 'person', 'top'],
            'attributes' => [
                'uid' => [
                    'add' => [],
                    'keep' => ['John12'],
                    'delete' => [],
                ],
                'givenName' => [
                    'add' => ['Peter'],
                    'keep' => ['John'],
                    'delete' => [],
                ],
            ],
        ]);
        $updateRequest2 = new Request\UpdateRequest('uid=John12,ou=users,ou=system', [
            'dn' => null,
            'objectClass' => ['inetOrgPerson', 'person', 'top'],
            'attributes' => [
                'uid' => [
                    'add' => [],
                    'keep' => ['John12'],
                    'delete' => [],
                ],
                'givenName' => [
                    'add' => [],
                    'keep' => ['John'],
                    'delete' => ['Peter'],
                ],
            ],
        ]);
        $updateRequest3 = new Request\UpdateRequest('uid=J0hn,ou=users,ou=system', [
            'dn' => 'uid=Jn0,ou=users,ou=system',
            'objectClass' => ['inetOrgPerson', 'person', 'top'],
            'attributes' => [
                'uid' => [
                    'add' => ['Jn0'],
                    'keep' => [],
                    'delete' => ['J0hn'],
                ],
                'givenName' => [
                    'add' => [],
                    'keep' => ['John'],
                    'delete' => [],
                ],
            ],
        ]);
        $newEntryRequest = new Request\NewEntryRequest('uid=John21,ou=users,ou=system', [
            'objectclass' => ['inetOrgPerson', 'person', 'top'],
            'uid' => ['John21'],
            'givenName' => ['John'],
        ]);

        $connection = $this->prophesize(ConnectionInterface::class);
        $connection->getBaseDn()->willReturn('ou=users,ou=system');
        $connection->execute(Argument::exact($queryRequest))->willReturn($queryResponse)->shouldBeCalledTimes(1);
        $connection->execute(Argument::exact($updateRequest0))->willReturn($successResponse)->shouldBeCalledTimes(1);
        $connection->execute(Argument::exact($updateRequest1))->willReturn($successResponse)->shouldBeCalledTimes(1);
        $connection->execute(Argument::exact($newEntryRequest))->willReturn($failureResponse)->shouldBeCalledTimes(1);
        $connection->execute(Argument::exact($updateRequest2))->willReturn($successResponse)->shouldBeCalledTimes(1);
        $connection->execute(Argument::exact($updateRequest3))->willReturn($successResponse)->shouldBeCalledTimes(1);

        ConnCallback::$conn = $connection->reveal();

        $builder = new ContainerBuilder();
        $builder->autowire(DummyUserRepository::class)
            ->setPublic(true);

        self::bootKernel([
            'builder' => $builder,
            'compiler_passes' => [new ConnectionCompilerPass([ConnCallback::class, 'getConn'])],
        ]);

        $repository = static::$container->get(DummyUserRepository::class);
        $users = iterator_to_array($repository->findByName('John'));

        $expectedUsers = [
            new DummyUser('uid=John00,ou=admin,ou=users,ou=system', ['John00'], ['John']),
            new DummyUser('uid=John,ou=users,ou=system', ['John'], ['John']),
            new DummyUser('uid=Jn0,ou=users,ou=system', ['Jn0'], ['John']),
            new DummyUser('uid=JJason,ou=users,ou=system', ['JJason'], ['John', 'Jason']),
            new DummyUser('uid=John12,ou=users,ou=system', ['John12'], ['John']),
        ];
        $this->assertEquals($expectedUsers, $users);

        foreach ($users as $user) {
            switch ($user->getUsername()[0]) {
                case 'John00':
                    break;
                case 'John':
                    break;
                case 'Jn0':
                    $user->setUsername('J0hn');
                    break;
                case 'JJason':
                    break;
                case 'John12':
                    $user->setName(['John', 'Peter']);
                    break;
            }
        }

        $newUser = new DummyUser('uid=John21,ou=users,ou=system', ['John21'], ['John']);
        $repository->persist($newUser);

        $this->expectException(\Exception::class);
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
