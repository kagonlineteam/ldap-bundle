<?php

namespace KAGOnlineTeam\LdapBundle\Tests\Fixtures;

use KAGOnlineTeam\LdapBundle\RepositoryInterface;

class DummyUserRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function find(string $dn): ?object
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findIn(string $dn): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return [];
    }
}
