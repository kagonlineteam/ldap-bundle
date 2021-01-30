<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Response;

use KAGOnlineTeam\LdapBundle\Response\FailureResponse;
use PHPUnit\Framework\TestCase;

class FailureResponseTest extends TestCase
{
    public function testValues()
    {
        $message = 'Cannot contact the Ldap server.';
        $response = new FailureResponse($message);

        $this->assertSame($message, $response->getResponse());
    }
}
