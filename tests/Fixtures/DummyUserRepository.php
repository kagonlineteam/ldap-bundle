<?php

namespace KAGOnlineTeam\LdapBundle\Tests\Fixtures;

use KAGOnlineTeam\LdapBundle\RepositoryInterface;

class DummyUserRepository implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function find(string $dn): ?object
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function findIn(string $dn): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(): array
    {
        return [];
    }
}