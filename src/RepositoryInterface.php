<?php

namespace KAGOnlineTeam\LdapBundle;

/**
 * Generic interface for all repository services.
 *
 * @author Jan Flaßkamp
 */
interface RepositoryInterface
{
    /**
     * @return string The entry class the repository is associated with
     */
    public function getClass(): string;

    public function find(string $dn): ?object;

    public function findAll(): iterable;

    public function persist(object $entry): void;

    public function remove(object $entry): void;

    public function commit(): void;
}
