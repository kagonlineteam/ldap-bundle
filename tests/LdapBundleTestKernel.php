<?php

namespace KAGOnlineTeam\LdapBundle\Tests;

use KAGOnlineTeam\LdapBundle\KAGOnlineTeamLdapBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class LdapBundleTestKernel extends BaseKernel
{
    private $builder;
    private $routes;
    private $extraBundles;
    private $compilerPasses;

    /**
     * @param array             $routes  Routes to be added to the container e.g. ['name' => 'path']
     * @param BundleInterface[] $bundles Additional bundles to be registered e.g. [new Bundle()]
     */
    public function __construct(ContainerBuilder $builder = null, array $routes = [], array $bundles = [], array $compilerPasses = [], array $publicServices = [])
    {
        $this->builder = $builder;
        $this->routes = $routes;
        $this->extraBundles = $bundles;
        $this->compilerPasses = $compilerPasses;

        $this->compilerPasses[] = new class($publicServices) implements CompilerPassInterface {
            private $definitions;

            public function __construct(array $definitions)
            {
                $this->definitions = $definitions;
            }

            public function process(ContainerBuilder $container)
            {
                foreach ($this->definitions as $definition) {
                    $container->getDefinition($this->definition)
                        ->setPublic(true);
                }
            }
        };

        parent::__construct('test', true);
    }

    public function registerBundles(): iterable
    {
        return array_merge(
            $this->extraBundles,
            [
                new FrameworkBundle(),
                new KAGOnlineTeamLdapBundle(),
            ]
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        if (null === $this->builder) {
            $this->builder = new ContainerBuilder();
        }

        $builder = $this->builder;

        $loader->load(function (ContainerBuilder $container) use ($builder) {
            $container->merge($builder);
            $container->loadFromExtension(
                'framework',
                [
                    'secret' => 'foo',
                    'router' => [
                        'resource' => 'kernel::loadRoutes',
                        'type' => 'service',
                        'utf8' => true,
                    ],
                ]
            );
            $container->loadFromExtension(
                'kagonlineteam_ldap',
                [
                    'ldap_url' => 'ldaps://example.com:636',
                    'ldap_bind' => 'cn=administrator,dc=example,dc=com?passphrase',
                    'base_dn' => 'ou=users,ou=system',
                ]
            );

            $container->register('kernel', static::class)
                ->setPublic(true)
            ;
        });
    }

    public function loadRoutes(LoaderInterface $loader): RouteCollection
    {
        $routes = new RouteCollection();

        foreach ($this->routes as $name => $path) {
            $routes->add($name, new Route($path));
        }

        return $routes;
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().\DIRECTORY_SEPARATOR.'LdapBundleTests'.spl_object_hash($this).\DIRECTORY_SEPARATOR.'cache';
    }

    public function getLogDir()
    {
        return sys_get_temp_dir().\DIRECTORY_SEPARATOR.'LdapBundleTests'.spl_object_hash($this).\DIRECTORY_SEPARATOR.'logs';
    }

    protected function build(ContainerBuilder $container)
    {
        foreach ($this->compilerPasses as $compilerPass) {
            $container->addCompilerPass($compilerPass);
        }
    }
}
