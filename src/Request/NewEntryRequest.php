<?php

namespace KAGOnlineTeam\LdapBundle\Request;

class NewEntryRequest implements RequestInterface
{
    private $dn;
    private $attributes = [];

    public function __construct(string $dn, array $attributes)
    {
        $this->dn = $dn;
        $this->attributes = $attributes;
    }

    public function getDn(): string
    {
        return $this->dn;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
