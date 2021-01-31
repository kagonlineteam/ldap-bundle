<?php

namespace KAGOnlineTeam\LdapBundle\Response;

class EntriesResponse implements ResponseInterface
{
    private $entries;
    private $readOnly;

    public function __construct(iterable $entries, bool $readOnly = true)
    {
        $this->entries = $entries;
        $this->readOnly = $readOnly;
    }

    public function getEntries(): iterable
    {
        return $this->entries;
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }
}
