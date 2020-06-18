<?php

namespace KAGOnlineTeam\LdapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Jan Flaßkamp
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('kagonlineteam_ldap');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->end();

        return $treeBuilder;
    }
}
