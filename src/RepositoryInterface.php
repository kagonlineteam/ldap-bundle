<?php

namespace KAGOnlineTeam\LdapBundle;

/**
 * Repository interface for all repositories.
 *
 * @author Jan Flaßkamp
 */
interface RepositoryInterface
{
    /**
     * Finds an entry by a distinguished name.
     *
     * @param string $dn The distinguished name
     *
     * @return object|null An entry object if found
     */
    public function find(string $dn): ?object;

    /**
     * Finds all entries in the subtree of a DN.
     *
     * @param string $dn The distinguished name
     *
     * @return array An array of entry objects
     */
    public function findIn(string $dn): array;

    /**
     * Finds all entries.
     *
     * @param string $dn The distinguished name
     *
     * @return array An array of entry objects
     */
    public function findAll(): array;
}
