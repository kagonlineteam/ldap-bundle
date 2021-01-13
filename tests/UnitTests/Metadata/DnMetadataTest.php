<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Metadata;

use KAGOnlineTeam\LdapBundle\Metadata\DnMetadata;
use PHPUnit\Framework\TestCase;

class DnMetadataTest extends TestCase
{
    public function testValues()
    {
        $metadata = new DnMetadata('dn');

        $this->assertSame('dn', $metadata->getProperty());
    }
}
