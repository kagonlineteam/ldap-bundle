<?php

namespace KAGOnlineTeam\LdapBundle;

use KAGOnlineTeam\LdapBundle\Metadata\Factory\MetadataFactoryInterface;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;
use Exception;

class EntryManager implements EntryManagerInterface
{
    private $metadataFactory;

    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadata(string $class): ClassMetadataInterface
    {
        return $this->metadataFactory->create($class);
    }

    /**
     * {@inheritDoc}
     */
    public function getRepository(string $class): RepositoryInterface
    {
        throw new Exception("Repositories are yet to be implemented.");
    }

    /**
     * {@inheritDoc}
     */
    public function save(object $entry): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function remove(object $entry): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function commit(): void
    {
    }
}