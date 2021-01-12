<?php

namespace KAGOnlineTeam\LdapBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Jan Flaßkamp
 */
final class KAGOnlineTeamLdapExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('ldap.yaml');
        $loader->load('metadata.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        // Inject the Ldap server details into the connection.
        $connection = $container->findDefinition('kagonlineteam_ldap.connection_factory');
        $connection->setArgument(0, 'symfony_ldap');
        $connection->setArgument(1, $config['ldap_url']);
        $connection->setArgument(2, $config['ldap_bind']);
    }

    public function getAlias()
    {
        return 'kagonlineteam_ldap';
    }
}
