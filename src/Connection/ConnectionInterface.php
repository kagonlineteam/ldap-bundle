<?php

namespace KAGOnlineTeam\LdapBundle\Connection;

use KAGOnlineTeam\LdapBundle\Request\RequestInterface;
use KAGOnlineTeam\LdapBundle\Response\ResponseInterface;

/**
 * @author Jan Flaßkamp
 */
interface ConnectionInterface
{
    /**
     * @return bool true if a connection to the Ldap server has been established
     */
    public function isConnected(): bool;

    /**
     * Connects this instance to the configured Ldap server.
     */
    public function connect(): void;

    /**
     * Disconnects this instance from the Ldap server.
     */
    public function disconnect(): void;

    /**
     * @return string 
     */
    public function getBaseDn(): string;

    /**
     * Executes a request and returns the response from the Ldap server.
     */
    public function execute(RequestInterface $request): ResponseInterface;
}
