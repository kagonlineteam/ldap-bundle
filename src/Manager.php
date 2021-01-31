<?php

namespace KAGOnlineTeam\LdapBundle;

use KAGOnlineTeam\LdapBundle\Connection\ConnectionInterface;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use KAGOnlineTeam\LdapBundle\Metadata\Factory\MetadataFactoryInterface;
use KAGOnlineTeam\LdapBundle\Query\Query;
use KAGOnlineTeam\LdapBundle\Request\RequestInterface;
use KAGOnlineTeam\LdapBundle\Response\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Manager implements ManagerInterface
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    public function __construct(MetadataFactoryInterface $metadataFactory, ConnectionInterface $connection)
    {
        $this->metadataFactory = $metadataFactory;
        $this->connection = $connection;
        $this->dispatcher = new EventDispatcher();
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseDn(): string
    {
        return $this->connection->getBaseDn();
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(string $class): ClassMetadata
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
    public function query(RequestInterface $request): ResponseInterface
    {
        return $this->connection->execute($request);
    }

    /**
     * Tightly coupled with Worker::createRequests().
     */
    public function update(\Generator $reqGen): void
    {
        $reqGen->rewind();
        while ($reqGen->valid()) {
            $response = $this->connection->execute($reqGen->current());
            if ($response instanceof Response\FailureResponse) {
                $reqGen->throw(new \Exception($response->getMessage()));
            }
            $reqGen->next();
        }
    }
}
