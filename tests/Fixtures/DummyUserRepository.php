<?php

namespace KAGOnlineTeam\LdapBundle\Tests\Fixtures;

use KAGOnlineTeam\LdapBundle\EntryManagerInterface;
use KAGOnlineTeam\LdapBundle\EntryRepository;
use KAGOnlineTeam\LdapBundle\Query\Options;

class DummyUserRepository extends EntryRepository
{
    public function __construct(EntryManagerInterface $em)
    {
        parent::__construct($em, DummyUser::class);
    }

    public function findByRole(string $role): array
    {
        $query = $this->createQueryBuilder()
            ->filterEquality()
                ->on('roles')
                ->with($role)
            ->end()
            ->scope(Options::SCOPE_SUB)
            ->make();

        return $this->execute($query);
    }
}
