<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Request;

use KAGOnlineTeam\LdapBundle\Request\UpdateRequest;
use PHPUnit\Framework\TestCase;

class UpdateRequestTest extends TestCase
{
    public function testValues()
    {
        $dn = 'cn=Mueller,ou=2020,ou=users,ou=system';
        $changeSet = [
            'dn' => 'cn=CMueller,ou=2020,ou=users,ou=system',
            'attributes' => [
                'cn' => [
                    'add' => ['CMueller'],
                    'keep' => [],
                    'delete' => ['Mueller'],
                ],
                'givenName' => [
                    'add' => ['Christoph'],
                    'keep' => ['Wilhelm'],
                    'delete' => ['Christoff'],
                ],
                'sn' => [
                    'add' => [],
                    'keep' => ['Mueller'],
                    'delete' => [],
                ],
            ],
        ];
        $request = new UpdateRequest($dn, $changeSet);

        $this->assertSame($dn, $request->getDn());
        $this->assertSame($changeSet, $request->getChangeSet());
    }
}
