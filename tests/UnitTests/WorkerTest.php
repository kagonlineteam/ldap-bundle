<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests;

use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use KAGOnlineTeam\LdapBundle\Request;
use KAGOnlineTeam\LdapBundle\Response\EntriesResponse;
use KAGOnlineTeam\LdapBundle\Serializer\SerializerInterface;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use KAGOnlineTeam\LdapBundle\Worker;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class WorkerTest extends TestCase
{
    public function testQuery(): void
    {
        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getObjectClasses()->willReturn(['inetOrgPerson', 'person', 'top']);

        $john = (object) ['cn' => ['John Smith']];
        $gallagher = (object) ['sn' => ['Gallagher']];
        $williams = (object) ['sn' => ['Williams']];

        $serializer = $this->prophesize(SerializerInterface::class);
        $serializer->denormalize(Argument::type('string'), Argument::type('array'))->will(function ($args) {
            return (object) $args[1];
        });
        $serializer->normalize(Argument::exact((object) ['cn' => ['John Smith']]))->willReturn(['dn' => 'cn=John+Smith,ou=system', 'objectclasses' => ['inetOrgPerson', 'person', 'top'], 'attributes' => ['cn' => ['John Smith']]]);
        $serializer->normalize(Argument::exact((object) ['sn' => ['Gallagher']]))->willReturn(['dn' => 'sn=Gallagher,ou=users,ou=system', 'objectclasses' => ['inetOrgPerson', 'person', 'top'], 'attributes' => ['sn' => ['Gallagher']]]);

        $worker = new Worker($metadata->reveal(), $serializer->reveal());
        $this->assertSame([], \iterator_to_array($worker->fetchLatest()));

        $response1 = $this->prophesize(EntriesResponse::class);
        $response1->getEntries()->willReturn([
            ['cn=John+Smith,ou=system', ['inetOrgPerson', 'person', 'top'], ['cn' => ['John Smith']]],
            ['sn=Gallagher,ou=users,ou=system', ['inetOrgPerson', 'person', 'top'], ['sn' => ['Gallagher']]],
            ['sn=Williams', ['person', 'top'], ['sn' => ['Williams']]],
        ]);
        $response1->isReadOnly()->willReturn(false);
        $worker->update($response1->reveal());
        
        $this->assertEquals([$gallagher, $john], \iterator_to_array($worker->fetchLatest()));
        $this->assertSame([], \iterator_to_array($worker->fetchLatest()));

        $response2 = $this->prophesize(EntriesResponse::class);
        $response2->getEntries()->willReturn([
            ['cn=admin,ou=system', ['top'], ['cn' => ['admin']]],
            ['sn=Williams,ou=users,ou=system', ['inetOrgPerson', 'person', 'top'], ['sn' => ['Williams']]],
        ]);
        $response2->isReadOnly()->willReturn(true);
        $worker->update($response2->reveal());

        $this->assertEquals([$williams], \iterator_to_array($worker->fetchLatest()));

        $williams->sn = ['Bill'];
        $this->assertSame([], \iterator_to_array($worker->createRequests()));
    }

    public function testMark(): void
    {
        $object = (object) ['cn' => ['User']];

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getObjectClasses()->willReturn(['top', 'person']);
        $serializer = $this->prophesize(SerializerInterface::class);
        $serializer->denormalize('uid=User,ou=users', ['cn' => ['User']])->willReturn($object)->shouldBeCalledTimes(1);
        $response = $this->prophesize(EntriesResponse::class);
        $response->getEntries()->willReturn([
            ['uid=User,ou=users', ['top', 'person'], ['cn' => ['User']]],
        ]);
        $response->isReadOnly()->willReturn(false);
        
        $worker = new Worker($metadata->reveal(), $serializer->reveal());
        $worker->update($response->reveal());
        $user = $worker->fetchLatest();
    }

    public function testCreateRequests(): void
    {
        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getObjectClasses()->willReturn(['person', 'top']);

        $serializer = $this->prophesize(SerializerInterface::class);
        $serializer->denormalize(Argument::type('string'), Argument::type('array'))->will(function ($args) {
            $object = (object) $args[1];
            $object->dn = $args[0];
            return $object;
        });
        $serializer->normalize(Argument::type(\stdClass::class))->will(function ($args) {
            $dn = $args[0]->dn;
            unset($args[0]->dn);

            return [
                'dn' => $dn,
                'objectclasses' => ['person', 'top'],
                'attributes' => (array) $args[0],
            ];
        });

        $worker = new Worker($metadata->reveal(), $serializer->reveal());

        $response = $this->prophesize(EntriesResponse::class);
        $response->getEntries()->willReturn([
            ['sn=Smith,ou=users,ou=system', ['person', 'top'], ['sn' => ['Smith'], 'givenName' => ['Noah']]],
            ['sn=Gallagher,ou=users,ou=system', ['person', 'top'], ['sn' => ['Gallagher'], 'givenName' => ['Liam']]],
            ['sn=Williams,ou=users,ou=system', ['person', 'top'], ['sn' => ['Williams'], 'givenName' => ['Olivia']]],
            ['sn=Jones,ou=users,ou=system', ['person', 'top'], ['sn' => ['Jones'], 'givenName' => ['Luke']]],
            ['sn=Garcia,ou=users,ou=system', ['person', 'top'], ['sn' => ['Garcia'], 'givenName' => ['Alicee']]],
        ]);
        $response->isReadOnly()->willReturn(false);
        $worker->update($response->reveal());
        
        $expectedFetch = [
            (object) ['dn' => 'sn=Garcia,ou=users,ou=system', 'sn' => ['Garcia'], 'givenName' => ['Alicee']],
            (object) ['dn' => 'sn=Jones,ou=users,ou=system', 'sn' => ['Jones'], 'givenName' => ['Luke']],
            (object) ['dn' => 'sn=Williams,ou=users,ou=system', 'sn' => ['Williams'], 'givenName' => ['Olivia']],
            (object) ['dn' => 'sn=Gallagher,ou=users,ou=system', 'sn' => ['Gallagher'], 'givenName' => ['Liam']],
            (object) ['dn' => 'sn=Smith,ou=users,ou=system', 'sn' => ['Smith'], 'givenName' => ['Noah']],
        ];
        $this->assertEquals($expectedFetch, ($entries = \iterator_to_array($worker->fetchLatest())));

        $entries[0]->givenName = ['Alice'];

        $generator = $worker->createRequests();
        $generator->rewind();

        $this->assertEquals(new Request\UpdateRequest('sn=Garcia,ou=users,ou=system', [
            'dn' => null,
            'objectClass' => ['person', 'top'],
            'attributes' => [
                'sn' => ['add' => [], 'keep' => ['Garcia'], 'delete' => []],
                'givenName' => ['add' => ['Alice'], 'keep' => [], 'delete' => ['Alicee']],
            ],
        ]), $generator->current());

        $generator->next();
        $this->assertSame(false, $generator->valid());
    }
}
