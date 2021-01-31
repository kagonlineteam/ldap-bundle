<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Connection;

use KAGOnlineTeam\LdapBundle\Connection\SymfonyConnection;
use KAGOnlineTeam\LdapBundle\Request\DeleteRequest;
use KAGOnlineTeam\LdapBundle\Request\NewEntryRequest;
use KAGOnlineTeam\LdapBundle\Request\QueryRequest;
use KAGOnlineTeam\LdapBundle\Request\RequestInterface;
use KAGOnlineTeam\LdapBundle\Request\UpdateRequest;
use KAGOnlineTeam\LdapBundle\Response\EntriesResponse;
use KAGOnlineTeam\LdapBundle\Response\FailureResponse;
use KAGOnlineTeam\LdapBundle\Response\SuccessResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Ldap\Adapter\CollectionInterface;
use Symfony\Component\Ldap\Adapter\ExtLdap\EntryManager;
use Symfony\Component\Ldap\Adapter\ExtLdap\UpdateOperation;
use Symfony\Component\Ldap\Adapter\QueryInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Ldap\LdapInterface;

class SymfonyConnectionTest extends TestCase
{
    public function testValues(): void
    {
        $ldap = $this->prophesize(LdapInterface::class);
        $ldap->bind('cn=admin,dc=example,dc=com', 'passwd')->shouldBeCalledTimes(1);

        $connection = new SymfonyConnection($ldap->reveal(), 'cn=admin,dc=example,dc=com?passwd', 'ou=users');
        $this->assertSame('ou=users', $connection->getBaseDn());

        $this->expectException(\RuntimeException::class);
        $request = $this->prophesize(RequestInterface::class);
        $connection->execute($request->reveal());
    }

    public function testQuery(): void
    {
        $collection = $this->prophesize(CollectionInterface::class);
        $collection->getIterator()->will(function () {
            yield new Entry('cn=0,ou=users', ['ObjectClass' => ['person'], 'cn' => ['0']]);
            yield new Entry('cn=1,ou=users', ['ObjectClass' => ['person'], 'cn' => ['1']]);
            yield new Entry('cn=2,ou=users', ['ObjectClass' => ['person'], 'cn' => ['2']]);
        });

        $query = $this->prophesize(QueryInterface::class);
        $query->execute()->willReturn($collection->reveal());

        $ldap = $this->prophesize(LdapInterface::class);
        $ldap->bind('cn=admin,dc=example,dc=com', 'passwd')->shouldBeCalledTimes(1);
        $ldap->query('dn', 'filter', ['options'])->willReturn($query->reveal())->shouldBeCalledTimes(1);

        $connection = new SymfonyConnection($ldap->reveal(), 'cn=admin,dc=example,dc=com?passwd', 'ou=users');
        $response = $connection->execute(new QueryRequest('dn', 'filter', ['options'], false));

        $this->assertEquals(new EntriesResponse(getTestQueryResultGenerator(), false), $response);
    }

    public function testAdd(): void
    {
        $ldap = $this->prophesize(LdapInterface::class);
        $ldap->bind('cn=admin,dc=example,dc=com', 'passwd')->shouldBeCalledTimes(1);

        $manager = $this->prophesize(EntryManager::class);
        $manager->add(new Entry('cn=NewOne,ou=users', [
            'ObjectClass' => ['top'],
            'cn' => ['NewOne'],
        ]))->shouldBeCalledTimes(1);
        $ldap->getEntryManager()->willReturn($manager->reveal());

        $connection = new SymfonyConnection($ldap->reveal(), 'cn=admin,dc=example,dc=com?passwd', 'ou=users');
        $response = $connection->execute(new NewEntryRequest('cn=NewOne,ou=users', [
            'ObjectClass' => ['top'],
            'cn' => ['NewOne'],
        ]));

        $this->assertEquals(new SuccessResponse(), $response);
    }

    public function testAddException(): void
    {
        $ldap = $this->prophesize(LdapInterface::class);
        $ldap->bind('cn=admin,dc=example,dc=com', 'passwd')->shouldBeCalledTimes(1);

        $manager = $this->prophesize(EntryManager::class);
        $manager->add(new Entry('cn=NewOne,ou=users', [
            'ObjectClass' => ['top'],
            'cn' => ['NewOne'],
        ]))->will(function ($args) {
            throw new LdapException('Entry could not be added.');
        })->shouldBeCalledTimes(1);
        $ldap->getEntryManager()->willReturn($manager->reveal());

        $connection = new SymfonyConnection($ldap->reveal(), 'cn=admin,dc=example,dc=com?passwd', 'ou=users');
        $response = $connection->execute(new NewEntryRequest('cn=NewOne,ou=users', [
            'ObjectClass' => ['top'],
            'cn' => ['NewOne'],
        ]));

        $this->assertEquals(new FailureResponse('Entry could not be added.'), $response);
    }

    public function testUpdate(): void
    {
        $ldap = $this->prophesize(LdapInterface::class);
        $ldap->bind('cn=admin,dc=example,dc=com', 'passwd')->shouldBeCalledTimes(1);

        $manager = $this->prophesize(EntryManager::class);
        $manager->applyOperations('cn=John,ou=employees,ou=users', [
            new UpdateOperation(\LDAP_MODIFY_BATCH_REPLACE, 'sn', ['Smith']),
            new UpdateOperation(\LDAP_MODIFY_BATCH_REPLACE, 'telephoneNumber', ['034313254', '004352345']),
        ])->shouldBeCalledTimes(1);
        $ldap->getEntryManager()->willReturn($manager->reveal());

        $connection = new SymfonyConnection($ldap->reveal(), 'cn=admin,dc=example,dc=com?passwd', 'ou=users');
        $response = $connection->execute(new UpdateRequest('cn=John,ou=employees,ou=users', [
            'dn' => null,
            'attributes' => [
                'cn' => ['add' => [], 'keep' => ['John'], 'delete' => []],
                'sn' => ['add' => ['Smith'], 'keep' => [], 'delete' => ['Roger']],
                'telephoneNumber' => ['add' => ['004352345'], 'keep' => ['034313254'], 'delete' => []],
            ],
        ]));

        $this->assertEquals(new SuccessResponse(), $response);
    }

    public function testMove(): void
    {
        $ldap = $this->prophesize(LdapInterface::class);
        $ldap->bind('cn=admin,dc=example,dc=com', 'passwd')->shouldBeCalledTimes(1);

        $manager = $this->prophesize(EntryManager::class);
        $manager->remove(new Entry('cn=John,ou=employees,ou=users'))->shouldBeCalledTimes(1);
        $manager->add(new Entry('cn=Peter,ou=employees,ou=users', [
            'objectClass' => ['person', 'top'],
            'cn' => ['John', 'Peter'],
            'sn' => ['Smith'],
            'telephoneNumber' => ['034313254', '004352345'],
        ]))->shouldBeCalledTimes(1);
        $ldap->getEntryManager()->willReturn($manager->reveal());

        $connection = new SymfonyConnection($ldap->reveal(), 'cn=admin,dc=example,dc=com?passwd', 'ou=users');
        $response = $connection->execute(new UpdateRequest('cn=John,ou=employees,ou=users', [
            'dn' => 'cn=Peter,ou=employees,ou=users',
            'objectClass' => ['person', 'top'],
            'attributes' => [
                'cn' => ['add' => ['Peter'], 'keep' => ['John'], 'delete' => []],
                'sn' => ['add' => ['Smith'], 'keep' => [], 'delete' => ['Roger']],
                'telephoneNumber' => ['add' => ['004352345'], 'keep' => ['034313254'], 'delete' => []],
            ],
        ]));

        $this->assertEquals(new SuccessResponse(), $response);
    }

    public function testUpdateException(): void
    {
        $ldap = $this->prophesize(LdapInterface::class);
        $ldap->bind('cn=admin,dc=example,dc=com', 'passwd')->shouldBeCalledTimes(1);

        $manager = $this->prophesize(EntryManager::class);
        $manager->remove(new Entry('cn=John,ou=employees,ou=users'))->will(function ($args) {
            throw new LdapException('Unable to remove entry.');
        })->shouldBeCalledTimes(1);
        $ldap->getEntryManager()->willReturn($manager->reveal());

        $connection = new SymfonyConnection($ldap->reveal(), 'cn=admin,dc=example,dc=com?passwd', 'ou=users');
        $response = $connection->execute(new UpdateRequest('cn=John,ou=employees,ou=users', [
            'dn' => 'cn=Peter,ou=employees,ou=users',
            'objectClass' => ['person', 'top'],
            'attributes' => [
                'cn' => ['add' => ['Peter'], 'keep' => ['John'], 'delete' => []],
                'sn' => ['add' => ['Smith'], 'keep' => [], 'delete' => ['Roger']],
                'telephoneNumber' => ['add' => ['004352345'], 'keep' => ['034313254'], 'delete' => []],
            ],
        ]));

        $this->assertEquals(new FailureResponse('Unable to remove entry.'), $response);
    }

    public function testUpdateDelayedException(): void
    {
        $ldap = $this->prophesize(LdapInterface::class);
        $ldap->bind('cn=admin,dc=example,dc=com', 'passwd')->shouldBeCalledTimes(1);

        $manager = $this->prophesize(EntryManager::class);
        $manager->remove(new Entry('cn=John,ou=employees,ou=users'))->shouldBeCalledTimes(1);
        $manager->add(new Entry('cn=Peter,ou=employees,ou=users', [
            'objectClass' => ['person', 'top'],
            'cn' => ['John', 'Peter'],
            'sn' => ['Smith'],
            'telephoneNumber' => ['034313254', '004352345'],
        ]))->will(function ($args) {
            throw new LdapException('Unable to add new entry.');
        })->shouldBeCalledTimes(1);
        $manager->add(new Entry('cn=John,ou=employees,ou=users', [
            'objectClass' => ['person', 'top'],
            'cn' => ['John'],
            'sn' => ['Roger'],
            'telephoneNumber' => ['034313254'],
        ]))->shouldBeCalledTimes(1);
        $ldap->getEntryManager()->willReturn($manager->reveal());

        $connection = new SymfonyConnection($ldap->reveal(), 'cn=admin,dc=example,dc=com?passwd', 'ou=users');
        $response = $connection->execute(new UpdateRequest('cn=John,ou=employees,ou=users', [
            'dn' => 'cn=Peter,ou=employees,ou=users',
            'objectClass' => ['person', 'top'],
            'attributes' => [
                'cn' => ['add' => ['Peter'], 'keep' => ['John'], 'delete' => []],
                'sn' => ['add' => ['Smith'], 'keep' => [], 'delete' => ['Roger']],
                'telephoneNumber' => ['add' => ['004352345'], 'keep' => ['034313254'], 'delete' => []],
            ],
        ]));

        $this->assertEquals(new FailureResponse('Unable to add new entry.'), $response);
    }

    public function testRemove(): void
    {
        $ldap = $this->prophesize(LdapInterface::class);
        $ldap->bind('cn=admin,dc=example,dc=com', 'passwd')->shouldBeCalledTimes(1);

        $manager = $this->prophesize(EntryManager::class);
        $manager->remove(new Entry('cn=Not\\20Needed'))->shouldBeCalledTimes(1);
        $ldap->getEntryManager()->willReturn($manager->reveal());

        $connection = new SymfonyConnection($ldap->reveal(), 'cn=admin,dc=example,dc=com?passwd', 'ou=users');
        $response = $connection->execute(new DeleteRequest('cn=Not\\20Needed'));

        $this->assertEquals(new SuccessResponse(), $response);
    }

    public function testRemoveException(): void
    {
        $ldap = $this->prophesize(LdapInterface::class);
        $ldap->bind('cn=admin,dc=example,dc=com', 'passwd')->shouldBeCalledTimes(1);

        $manager = $this->prophesize(EntryManager::class);
        $manager->remove(new Entry('cn=Not\\20Needed'))->will(function ($args) {
            throw new LdapException('The operation failed.');
        })->shouldBeCalledTimes(1);
        $ldap->getEntryManager()->willReturn($manager->reveal());

        $connection = new SymfonyConnection($ldap->reveal(), 'cn=admin,dc=example,dc=com?passwd', 'ou=users');
        $response = $connection->execute(new DeleteRequest('cn=Not\\20Needed'));

        $this->assertEquals(new FailureResponse('The operation failed.'), $response);
    }
}

function getTestQueryResultGenerator(): \Generator
{
    yield ['cn=0,ou=users', ['person'], ['cn' => ['0']]];
    yield ['cn=1,ou=users', ['person'], ['cn' => ['1']]];
    yield ['cn=2,ou=users', ['person'], ['cn' => ['2']]];
}
