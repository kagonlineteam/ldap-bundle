<?php

namespace KAGOnlineTeam\LdapBundle\Tests\Fixtures;

use KAGOnlineTeam\LdapBundle\Annotation as Ldap;

/**
 * @Ldap\Entry(
 *     repositoryClass="KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUserRepository",
 *     objectClasses={
 *         "inetOrgPerson",
 *         "person",
 *         "top"
 *     }
 * )
 */
class DummyUser
{
    /**
     * @Ldap\DistinguishedName()
     */
    private $dn;

    /**
     * @Ldap\Attribute(description="uid")
     */
    private $username;

    /**
     * @Ldap\Attribute(description="givenName")
     */
    private $name;

    private $other;

    public function __construct(string $dn, $username, $name)
    {
        $this->dn = $dn;
        $this->username = $username;
        $this->name = $name;
    }

    public function getUsername(): array
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->dn = \str_replace($this->username, $username, $this->dn);
        $this->username = [$username];
    }

    public function getName(): array
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = [$name];
    }
}
