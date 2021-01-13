<?php

namespace KAGOnlineTeam\LdapBundle\Tests\Serializer\UnitTests;

use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use KAGOnlineTeam\LdapBundle\Metadata\DnMetadata;
use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;
use KAGOnlineTeam\LdapBundle\Serializer\ReflectionSerializer;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUserRepository;
use PHPUnit\Framework\TestCase;

class ReflectionSerializerTest extends TestCase
{
    /**
     * @dataProvider provideNormalize
     */
    public function testNormalize($metadata, array $data, object $expectedObject): void
    {
        $serializer = new ReflectionSerializer($metadata);

        $object = $serializer->denormalize($data['dn'], $data['attributes']);
        $this->assertEquals($expectedObject, $object);
        $normalized = $serializer->normalize($object);
        $this->assertSame($data, $normalized);
    }

    public function provideNormalize(): iterable
    {
        $usernameProperty = $this->prophesize(PropertyMetadata::class);
        $usernameProperty->getProperty()->willReturn('username');
        $usernameProperty->getAttribute()->willReturn('uid');

        $nameProperty = $this->prophesize(PropertyMetadata::class);
        $nameProperty->getProperty()->willReturn('name');
        $nameProperty->getAttribute()->willReturn('givenName');

        $dn = $this->prophesize(DnMetadata::class);
        $dn->getProperty()->willReturn('dn');

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getClass()->willReturn(DummyUser::class);
        $metadata->getRepositoryClass()->willReturn(DummyUserRepository::class);
        $metadata->getObjectClasses()->willReturn(['top', 'person', 'inetOrgPerson']);
        $metadata->getDn()->willReturn($dn->reveal());
        $metadata->getProperties()->willReturn([$usernameProperty->reveal(), $nameProperty->reveal()]);

        yield [
            $metadata->reveal(),
            [
                'dn' => 'uid=MMustermann,ou=users,ou=system,dc=example,dc=com',
                'objectclasses' => ['top', 'person', 'inetOrgPerson'],
                'attributes' => [
                    'uid' => ['MMustermann'],
                    'givenName' => ['Max'],
                ],
            ],
            new DummyUser('uid=MMustermann,ou=users,ou=system,dc=example,dc=com', ['MMustermann'], ['Max']),
        ];
    }
}
