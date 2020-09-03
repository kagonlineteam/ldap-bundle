<?php

namespace KAGOnlineTeam\LdapBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * @author Jan Flaßkamp
 */
final class KAGOnlineTeamLdapExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
    }

    public function getAlias()
    {
        return 'kagonlineteam_ldap';
    }
}
