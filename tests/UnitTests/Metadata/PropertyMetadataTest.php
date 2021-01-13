<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Metadata;

use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;
use PHPUnit\Framework\TestCase;

class PropertyMetadataTest extends TestCase
{
    public function testValues()
    {
        $metadata = new PropertyMetadata('username');
        $metadata->setAttribute('uid');

        $this->assertSame('username', $metadata->getProperty());
        $this->assertSame('uid', $metadata->getAttribute());
    }
}
