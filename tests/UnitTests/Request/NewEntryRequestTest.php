<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Request;

use KAGOnlineTeam\LdapBundle\Request\NewEntryRequest;
use PHPUnit\Framework\TestCase;

class NewEntryRequestTest extends TestCase
{
    public function testValues()
    {
        $dn = 'uid=Nutzer150,ou=benutzer';
        $attributes = [
            'ObjectClass' => ['top', 'person'],
            'cn' => ['Nutzer150'],
        ];

        $request = new NewEntryRequest($dn, $attributes);

        $this->assertSame($dn, $request->getDn());
        $this->assertSame($attributes, $request->getAttributes());
    }
}
