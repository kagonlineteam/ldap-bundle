<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Connection;

use KAGOnlineTeam\LdapBundle\Connection\ConnectionFactory;
use KAGOnlineTeam\LdapBundle\Connection\SymfonyConnection;
use PHPUnit\Framework\TestCase;

class ConnectionFactoryTest extends TestCase
{
    public function testGetSymfonyConnection()
    {
        $factory = new ConnectionFactory('symfony_ldap', 'ldaps://ds.example.com:389/dc=example,dc=com', 'cn=admin?passwd', 'ou=users');
        $connection = $factory->create();

        $this->assertSame(SymfonyConnection::class, \get_class($connection));
        $this->assertSame('ou=users', $connection->getBaseDn());
    }
}
