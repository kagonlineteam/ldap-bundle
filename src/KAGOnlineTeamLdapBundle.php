<?php

namespace KAGOnlineTeam\LdapBundle;

use KAGOnlineTeam\LdapBundle\DependencyInjection\KAGOnlineTeamLdapExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Jan Flaßkamp
 */
class KAGOnlineTeamLdapBundle extends Bundle
{
    /**
     * Inject the manager service.
     */
    public function boot()
    {
        $manager = new EntryManager(
            $this->container->get('kagonlineteam_ldap.metadata_factory')
        );

        $this->container->set('kagonlineteam_ldap.manager', $manager);
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new KAGOnlineTeamLdapExtension();
        }

        return $this->extension ?: null;
    }
}
