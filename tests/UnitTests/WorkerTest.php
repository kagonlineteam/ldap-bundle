<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests;

use KAGOnlineTeam\LdapBundle\Worker;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;
use KAGOnlineTeam\LdapBundle\Serializer\SerializerInterface;
use KAGOnlineTeam\LdapBundle\Request;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class WorkerTest extends TestCase
{
    public function testWorker(): void
    {
        $newUser = new DummyUser('uid=User1,ou=users', 'User1', 'User');

        $metadata = $this->prophesize(ClassMetadataInterface::class);
        $serializer = $this->prophesize(SerializerInterface::class);
        $serializer->normalize($newUser)->willReturn([
            'dn' => 'uid=User1,ou=users',
            'attributes' => [
                'objectClass' => ['inetOrgPerson', 'person', 'top'],
                'uid' => ['User1'],
                'givenName' => ['User'],
            ],
        ]);

        $worker = new Worker($metadata->reveal(), $serializer->reveal());

        $worker->mark($newUser, Worker::MARK_PERSISTENCE);

        $expectedRequests = [
            new Request\NewEntryRequest('uid=User1,ou=users', [
                'objectClass' => ['inetOrgPerson', 'person', 'top'],
                'uid' => ['User1'],
                'givenName' => ['User'],
            ]),
        ];

        $this->assertEquals($expectedRequests, \iterator_to_array($worker->createRequests()));
    }
}
