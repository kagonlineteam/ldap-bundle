<?php

namespace KAGOnlineTeam\LdapBundle;

use KAGOnlineTeam\LdapBundle\Query\Builder;
use KAGOnlineTeam\LdapBundle\Query\Options;
use KAGOnlineTeam\LdapBundle\Request\QueryRequest;

abstract class AbstractRepository implements RepositoryInterface
{
    private $manager;
    private $metadata;
    private $worker;

    public function __construct(ManagerInterface $manager, string $class, Worker $worker = null)
    {
        $this->manager = $manager;
        $this->metadata = $manager->getMetadata($class);

        $this->worker = $worker ?: new Worker($this->metadata);
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

        $entries = \iterator_to_array($this->execute($qb));
        return \count($entries) === 0 ? null : $entries[0];
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
        $this->worker->mark($entry, Worker::MARK_PERSISTENCE);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(object $entry): void
    {
        $this->worker->mark($entry, Worker::MARK_REMOVAL);
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): void
    {
        $this->manager->update($this->worker->createRequests());
    }

    /**
     * @return Builder A fresh query builder instance
     */
    protected function createQueryBuilder(): Builder
    {
        return new Builder($this->manager->getBaseDn(), $this->metadata);
    }

    protected function execute(QueryRequest $request): iterable
    {
        $this->worker->update(
            $this->manager->query($request)
        );

        return $this->worker->fetchLatest();
    }
}
