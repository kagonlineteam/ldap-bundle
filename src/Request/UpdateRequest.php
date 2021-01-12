<?php

namespace KAGOnlineTeam\LdapBundle\Request;

class UpdateRequest implements RequestInterface
{
    private $dn;
    private $changeSet;

    public function __construct(string $dn, array $changeSet)
    {
        $this->dn = $dn;
        $this->changeSet = $changeSet;
    }

    public function getDn(): string
    {
        return $this->dn;
    }

    public function getChangeSet(): array
    {
        return $this->changeSet;
    }
}