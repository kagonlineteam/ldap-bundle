<?php

namespace KAGOnlineTeam\LdapBundle\Tests\IntegrationTests\Query;

use KAGOnlineTeam\LdapBundle\Query\Builder;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use KAGOnlineTeam\LdapBundle\Tests\LdapBundleKernelTestCase as KernelTestCase;

class BuilderTest extends KernelTestCase
{
    public function testAddObjectClasses()
    {
        self::bootKernel();

        $manager = static::$container->get('kagonlineteam_ldap.manager');

        $query = (new Builder('ou=users,dc=example,dc=com', $manager->getMetadata(DummyUser::class)))
            ->filterEquality()
                ->with('username', 'FMüller')
            ->end()
            ->make();

        $this->assertSame('(&(objectClass=inetOrgPerson)(objectClass=person)(objectClass=top)(uid=FMüller))', $query->getFilter());
    }
    
    public function testPresenceFilter()
    {
        self::bootKernel();

        $manager = static::$container->get('kagonlineteam_ldap.manager');

        $query = (new Builder('ou=users,dc=example,dc=com', $manager->getMetadata(DummyUser::class)))
            ->filterPresence()
                ->on('username')
            ->end()
            ->make();

        $this->assertSame('(&(objectClass=inetOrgPerson)(objectClass=person)(objectClass=top)(uid=*))', $query->getFilter());
    }
}
