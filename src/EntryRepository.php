<?php

namespace KAGOnlineTeam\LdapBundle;

use KAGOnlineTeam\LdapBundle\Query\Builder;
use KAGOnlineTeam\LdapBundle\Query\Options;

class EntryRepository implements RepositoryInterface
{
    private $manager;
    private $metadata;

    public function __construct(EntryManagerInterface $manager, string $class)
    {
        $this->manager = $manager;
        $this->metadata = $manager->getMetadata($class);
    }

    /**
     * {@inheritdoc}
     */
    public function getClass(): string
    {
        return $this->metadata->getClass();
    }

    /**
     * Finds an entry by a distinguished name.
     *
     * @param string $dn The distinguished name
     *
     * @return object|null An entry object if found
     */
    public function find(string $dn): ?object
    {
        $qb = $this->createQueryBuilder()
            ->in($dn)
            ->scope(Options::SCOPE_BASE)
            ->make();

        return $this->execute($qb);
    }

    /**
     * Finds all entries.
     *
     * @param string $dn The distinguished name
     */
    public function findAll(): iterable
    {
        $query = $this->createQueryBuilder()
            ->in(Options::BASE_DN)
            ->scope(Options::SCOPE_BASE)
            ->make();

        return $this->execute($query);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(object $entry): void
    {
        $this->manager->persist($this->getClass(), $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(object $entry): void
    {
        $this->manager->remove($this->getClass(), $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): void
    {
        $this->manager->commit($this->getClass());
    }

    /**
     * @return Builder A fresh query builder instance
     */
    protected function createQueryBuilder(): Builder
    {
        return new Builder($this->manager->getBaseDn(), $this->metadata);
    }

    protected function execute(Query $query): iterable
    {
        return $this->manager->query($query, $this->getClass());
    }
}
