<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Metadata;

use KAGOnlineTeam\LdapBundle\Metadata\DnMetadata;
use PHPUnit\Framework\TestCase;

class DnMetadataTest extends TestCase
{
    public function testValues()
    {
        $metadata1 = new DnMetadata('dn', 'string');
        $metadata2 = new DnMetadata('id', DnMetadata::TYPE_OBJECT);

        $this->assertSame('dn', $metadata1->getProperty());
        $this->assertSame(DnMetadata::TYPE_STRING, $metadata1->getType());
        $this->assertSame('id', $metadata2->getProperty());
        $this->assertSame('object', $metadata2->getType());
    }

    public function testInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        new DnMetadata('dn', 'invalidType');
    }
}
