<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Request;

use KAGOnlineTeam\LdapBundle\Request\QueryRequest;
use PHPUnit\Framework\TestCase;

class QueryRequestTest extends TestCase
{
    public function testValues()
    {
        $dn = 'ou=users,ou=system';
        $filter = '(ObjectClass=*)';
        $options = ['scope' => 'one'];
        $readOnly = false;
        $request = new QueryRequest($dn, $filter, $options, $readOnly);

        $this->assertSame($dn, $request->getDn());
        $this->assertSame($filter, $request->getFilter());
        $this->assertSame($options, $request->getOptions());
        $this->assertSame($readOnly, $request->isReadOnly());
    }
}
