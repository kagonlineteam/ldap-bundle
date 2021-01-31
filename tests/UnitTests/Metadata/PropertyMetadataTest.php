<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Metadata;

use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;
use PHPUnit\Framework\TestCase;

class PropertyMetadataTest extends TestCase
{
    public function testValues()
    {
        $metadata = new PropertyMetadata('username', 'uid', 'array');

        $this->assertSame('username', $metadata->getProperty());
        $this->assertSame('uid', $metadata->getAttribute());
        $this->assertSame(PropertyMetadata::TYPE_ARRAY, $metadata->getType());
    }

    public function testInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        new PropertyMetadata('name', 'sn', 'invalidType');
    }
}
