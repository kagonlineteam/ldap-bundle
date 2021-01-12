<?php

namespace KAGOnlineTeam\LdapBundle\Request;

class DeleteRequest implements RequestInterface
{
    private $dn;

    public function __construct(string $dn)
    {
        $this->dn = $dn;
    }

    public function getDn(): string
    {
        return $this->dn;
    }
}
