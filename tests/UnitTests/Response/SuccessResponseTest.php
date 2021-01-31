<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Response;

use KAGOnlineTeam\LdapBundle\Response\SuccessResponse;
use PHPUnit\Framework\TestCase;

class SuccessResponseTest extends TestCase
{
    public function testInstantiation()
    {
        $response = new SuccessResponse();

        $this->expectNotToPerformAssertions();
    }
}
