<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Request;

use KAGOnlineTeam\LdapBundle\Request\DeleteRequest;
use PHPUnit\Framework\TestCase;

class DeleteRequestTest extends TestCase
{
    public function testValues()
    {
        $dn = 'uid=administrator,ou=users,dc=example,dc=com';
        $request = new DeleteRequest($dn);

        $this->assertSame($dn, $request->getDn());
    }
}
