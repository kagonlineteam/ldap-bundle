<?php

namespace KAGOnlineTeam\LdapBundle\Tests\Serializer\UnitTests;

use KAGOnlineTeam\LdapBundle\Serializer\ReflectionSerializer;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;
use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;
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
        $usernameProperty->getName()->willReturn('username');
        $usernameProperty->getAttribute()->willReturn('uid');
        $usernameProperty->getReflectionProperty()->willReturn(new \ReflectionProperty(DummyUser::class, 'username'));
        $usernameProperty = $usernameProperty->reveal();

        $nameProperty = $this->prophesize(PropertyMetadata::class);
        $nameProperty->getName()->willReturn('name');
        $nameProperty->getAttribute()->willReturn('givenName');
        $nameProperty->getReflectionProperty()->willReturn(new \ReflectionProperty(DummyUser::class, 'name'));
        $nameProperty = $nameProperty->reveal();

        $metadata = $this->prophesize(ClassMetadataInterface::class);
        $metadata->getClass()->willReturn(DummyUser::class);
        $metadata->getRepositoryClass()->willReturn(DummyUserRepository::class);
        $metadata->getProperties()->willReturn([$usernameProperty, $nameProperty]);
        $metadata->getDnProperty()->willReturn(new \ReflectionProperty(DummyUser::class, 'dn'));
        $metadata->getReflectionClass()->willReturn(new \ReflectionClass(DummyUser::class));
        $metadata->getObjectClasses()->willReturn(['top', 'person', 'inetOrgPerson']);
        $metadata = $metadata->reveal();

        yield [
            $metadata,
            [
                'dn' => 'uid=MMustermann,ou=users,ou=system,dc=example,dc=com',
                'objectclasses' => ['top', 'person', 'inetOrgPerson'],
                'attributes' => [
                    'uid' => ['MMustermann'],
                    'givenName' => ['Max'],
                ]
            ],
            new DummyUser('uid=MMustermann,ou=users,ou=system,dc=example,dc=com', ['MMustermann'], ['Max']),
        ];
    }
}
