<?php

namespace KAGOnlineTeam\LdapBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use KAGOnlineTeam\LdapBundle\EntryManagerInterface;
use function dirname;

/**
 * @author Jan Flaßkamp
 */
final class KAGOnlineTeamLdapExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('ldap.yaml');
        $loader->load('metadata.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
    }

    public function getAlias()
    {
        return 'kagonlineteam_ldap';
    }
}
