<?php

namespace KAGOnlineTeam\LdapBundle;

use Exception;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;
use KAGOnlineTeam\LdapBundle\Metadata\Factory\MetadataFactoryInterface;

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
    public function getMetadata(string $class): ClassMetadataInterface
    {
        return $this->metadataFactory->create($class);
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(string $class): RepositoryInterface
    {
        throw new Exception('Repositories are yet to be implemented.');
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
