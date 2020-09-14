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
}