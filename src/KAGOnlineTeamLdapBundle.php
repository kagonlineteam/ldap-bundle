<?php

namespace KAGOnlineTeam\LdapBundle;

use KAGOnlineTeam\LdapBundle\DependencyInjection\KAGOnlineTeamLdapExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Jan Flaßkamp
 */
class KAGOnlineTeamLdapBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new KAGOnlineTeamLdapExtension();
        }

        return $this->extension ?: null;
    }
}
