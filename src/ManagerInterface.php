<?php

namespace KAGOnlineTeam\LdapBundle;

use KAGOnlineTeam\LdapBundle\Exception\NoMetadataException;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;
use KAGOnlineTeam\LdapBundle\Query\Query;
use KAGOnlineTeam\LdapBundle\Request\RequestInterface;
use KAGOnlineTeam\LdapBundle\Response\ResponseInterface;

/**
 * Main service to obtain and manipulate entry objects.
 *
 * @author Jan Flaßkamp
 */
interface ManagerInterface
{
    /**
     * @return string The configured base dn for the connection
     */
    public function getBaseDn(): string;

    /**
     * Returns the class metadata for a given class.
     *
     * @param string $class The fully qualified class name
     *
     * @throws InvalidArgumentException If the given class does not exist
     * @throws NoMetadataException      If metadata for the class cannot be found
     */
    public function getMetadata(string $class): ClassMetadataInterface;

    /**
     * Returns the service id of the repository.
     *
     * @param string $class The fully qualified class name
     *
     * @throws InvalidArgumentException If the given class does not exist
     * @throws NoMetadataException      If metadata for the class cannot be found
     */
    public function getRepositoryId(string $class): string;

    /**
     * 
     */
    public function query(RequestInterface $request): ResponseInterface;
}
