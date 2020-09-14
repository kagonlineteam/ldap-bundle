<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Metadata;

use PHPUnit\Framework\TestCase;
use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use ReflectionProperty;

class PropertyMetadataTest extends TestCase
{
    public function testValues()
    {
        $reflection = new ReflectionProperty(DummyUser::class, "username");
        $metadata = new PropertyMetadata($reflection, "uid");

        $this->assertSame("username", $metadata->getName());
        $this->assertSame($reflection, $metadata->getReflectionProperty());
        $this->assertSame("uid", $metadata->getAttribute());
    }
}