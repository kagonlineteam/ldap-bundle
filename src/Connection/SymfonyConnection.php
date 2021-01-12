<?php

namespace KAGOnlineTeam\LdapBundle\Connection;

use KAGOnlineTeam\LdapBundle\Request\RequestInterface;
use KAGOnlineTeam\LdapBundle\Request\QueryRequest;
use KAGOnlineTeam\LdapBundle\Response\ResponseInterface;
use KAGOnlineTeam\LdapBundle\Response\EntriesResponse;

/**
 * An adapter for Symfonys Ldap component. 
 */
class SymfonyConnection implements ConnectionInterface
{
    private $ldapUrl;
    private $ldapBind;

    private $symLdap;

    public function __construct(string $ldapUrl, string $ldapBind)
    {
        $this->ldapUrl = $ldapUrl;
        $this->resolveBindCredentials($ldapBind);
    }

    public function isConnected(): bool
    {
        return isset($symLdap);
    }

    public function connect(): void
    {
        $this->symLdap = Ldap::create('ext_ldap', ['connection_string' => $this->ldapUrl]);
        $this->symLdap->bind(...$ldapBind);
    }

    public function disconnect(): void
    {
        
    }

    public function getBaseDn(): string
    {
        throw new \LogicException('Not yet implemented.');
    }

    public function execute(RequestInterface $request): ResponseInterface
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        if ($request instanceof QueryRequest) {
            $collection = $this->symLdap->query(
                $request->getBaseDn(), $request->getFilter(), $request->getOptions() 
            )->execute()->toArray();

            return new EntriesResponse($collection, $request->isReadOnly());
        }

        throw new \RuntimeException('Invalid request type.');
    }

    private function resolveBindCredentials(string $bindCredentials): void
    {
        $i = \strpos($bindCredentials, '?');
        $this->ldapBind = [
            \substr($bindCredentials, 0, $i),
            \substr($bindCredentials, $i+1),
        ];
    }
}
