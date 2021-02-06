<?php

namespace KAGOnlineTeam\LdapBundle\Request;

class BindRequest implements RequestInterface
{
    private $dn;
    private $password;

    public function __construct(string $dn, string $password)
    {
        $this->dn = $dn;
        $this->password = $password;
    }

    public function getDn(): string
    {
        return $this->dn;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
