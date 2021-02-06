<?php

namespace KAGOnlineTeam\LdapBundle;

use KAGOnlineTeam\LdapBundle\Connection\ConnectionFactory;
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
     * @var ConnectionFactory
     */
    private $connectionFactory;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    private $boundConnection = null;

    public function __construct(MetadataFactoryInterface $metadataFactory, ConnectionFactory $connectionFactory)
    {
        $this->metadataFactory = $metadataFactory;
        $this->connectionFactory = $connectionFactory;
        $this->dispatcher = new EventDispatcher();
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseDn(): string
    {
        $this->setUpConnection();

        return $this->boundConnection->getBaseDn();
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
    public function getRepositoryClass(string $class): string
    {
        return $this->getMetadata($class)->getRepositoryClass();
    }

    /**
     * {@inheritdoc}
     */
    public function query(RequestInterface $request): ResponseInterface
    {
        $this->setUpConnection();

        return $this->boundConnection->execute($request);
    }

    /**
     * Tightly coupled with Worker::createRequests().
     */
    public function update(\Generator $reqGen): void
    {
        $this->setUpConnection();

        $reqGen->rewind();
        while ($reqGen->valid()) {
            $response = $this->boundConnection->execute($reqGen->current());
            if ($response instanceof Response\FailureResponse) {
                $reqGen->throw(new \Exception($response->getMessage()));
            }
            $reqGen->next();
        }
    }

    private function setUpConnection(): void
    {
        if (null === $this->boundConnection) {
            $connection = $this->connectionFactory->create();
            $connection->connect();
            $connection->bind();

            $this->boundConnection = $connection;
        }
    }
}
