<?php

namespace KAGOnlineTeam\LdapBundle;

use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;
use KAGOnlineTeam\LdapBundle\Metadata\Factory\MetadataFactoryInterface;
use KAGOnlineTeam\LdapBundle\Query\Query;

class EntryManager implements EntryManagerInterface
{
    private $metadataFactory;

    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseDn(): string
    {
        throw new \LogicException('Not yet implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(string $class): ClassMetadataInterface
    {
        return $this->metadataFactory->create($class);
    }

    /**
     * {@inheritdoc}
     */
    public function getRepositoryId(string $class): string
    {
        return $this->getMetadata($class)->getRepositoryClass();
    }

    /**
     * {@inheritdoc}
     */
    public function query(Query $query): iterable
    {
    }

    /**
     * {@inheritdoc}
     */
    public function save(object $entry): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function remove(object $entry): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): void
    {
    }
}
