<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Response;

use KAGOnlineTeam\LdapBundle\Response\EntriesResponse;
use PHPUnit\Framework\TestCase;

class EntriesResponseTest extends TestCase
{
    public function testValues()
    {
        $entries = [
            ['uid=John12,ou=users,ou=system', [
                'top', 'person', 'inetOrgPerson',
                ], [
                    'uid' => ['John12'],
                    'givenName' => ['John'],
                ],
            ],
            ['uid=JJason,ou=users,ou=system', [
                'top', 'person', 'inetOrgPerson',
                ], [
                    'uid' => ['JJason'],
                    'givenName' => ['John', 'Jason'],
                ],
            ],
        ];
        $readOnly = false;
        $response = new EntriesResponse($entries, $readOnly);

        $this->assertSame($entries, $response->getEntries());
        $this->assertSame($readOnly, $response->isReadOnly());
    }
}
