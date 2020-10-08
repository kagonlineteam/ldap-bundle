<?php

namespace KAGOnlineTeam\LdapBundle\Query;

class Query
{
    private $dn;
    private $filter;
    private $options;

    public function __construct(string $dn, string $filter, array $options)
    {
        $this->dn = $dn;
        $this->filter = $filter;
        $this->options;
    }

    public function getDn(): string
    {
        return $this->dn;
    }

    public function getFilter(): string
    {
        return $this->filter;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
