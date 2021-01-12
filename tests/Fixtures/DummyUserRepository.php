<?php

namespace KAGOnlineTeam\LdapBundle\Tests\Fixtures;

use KAGOnlineTeam\LdapBundle\ManagerInterface;
use KAGOnlineTeam\LdapBundle\AbstractRepository;
use KAGOnlineTeam\LdapBundle\Query\Options;

class DummyUserRepository extends AbstractRepository
{
    public function __construct(ManagerInterface $manager)
    {
        parent::__construct($manager, DummyUser::class);
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
