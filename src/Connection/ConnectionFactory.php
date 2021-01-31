<?php

namespace KAGOnlineTeam\LdapBundle\Connection;

use Symfony\Component\Ldap\Ldap;

class ConnectionFactory
{
    private static $aliasMap = [
        'symfony_ldap' => SymfonyConnection::class,
    ];

    private $symLdapProxy;
    private $type;
    private $ldapUrl;
    private $credentials;
    private $baseDn;

    public function __construct($type, string $ldapUrl, string $credentials, string $baseDn = '')
    {
        $this->type = $type;
        $this->ldapUrl = $ldapUrl;
        $this->credentials = $credentials;
        $this->baseDn = $baseDn;
    }

    public function getConnection(): ConnectionInterface
    {
        if (\is_string($this->type)) {
            if (\array_key_exists($this->type, self::$aliasMap)) {

                switch ($this->type) {
                    case 'symfony_ldap':
                        return new static::$aliasMap[$this->type](Ldap::create('ext_ldap', ['connection_string' => $this->ldapUrl]), $this->credentials, $this->baseDn);   
                }
            } else {
                if (!class_exists($this->type)) {
                    throw new \InvalidArgumentException(sprintf('Undefined class "%s".', $this->type));
                }

                $reflection = new \ReflectionClass($this->type);
                if (!$reflection->isInstantiable() || !$reflection->implementsInterface(ConnectionInterface::class)) {
                    throw new \InvalidArgumentException(sprintf('Class "%s" cannot be used as connection.', $this->type));
                }

                $class = $this->type;
                return new $class($this->ldapUrl, $this->credentials);
            }  
        }

        if (\is_callable($this->type)) {
            return \call_user_func($this->type);
        }

        throw new \InvalidArgumentException('Invalid type given.');
    }
}
