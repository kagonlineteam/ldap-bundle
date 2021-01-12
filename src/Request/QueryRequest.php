<?php

namespace KAGOnlineTeam\LdapBundle\Request;

class QueryRequest implements RequestInterface
{
    private $dn;
    private $filter;
    private $options;
    private $readOnly;

    public function __construct(string $dn, string $filter, array $options, bool $readOnly = true)
    {
        $this->dn = $dn;
        $this->filter = $filter;
        $this->options;
        $this->readOnly = $readOnly;
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

    public function isReadOnly(): bool
    {
        return $readOnly;
    }
}
