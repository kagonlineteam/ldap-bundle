<?php

namespace KAGOnlineTeam\LdapBundle\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use KAGOnlineTeam\LdapBundle\KAGOnlineTeamLdapBundle;
use function array_merge;
use function sys_get_temp_dir;
use function spl_object_hash;
use const DIRECTORY_SEPARATOR;

/**
 * 
 */
class LdapBundleTestKernel extends BaseKernel
{
    private $builder;
    private $routes;
    private $extraBundles;

    /**
     * @param array             $routes  Routes to be added to the container e.g. ['name' => 'path']
     * @param BundleInterface[] $bundles Additional bundles to be registered e.g. [new Bundle()]
     */
    public function __construct(ContainerBuilder $builder = null, array $routes = [], array $bundles = [])
    {
        $this->builder = $builder;
        $this->routes = $routes;
        $this->extraBundles = $bundles;

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
        return sys_get_temp_dir().DIRECTORY_SEPARATOR.'cache'.spl_object_hash($this);
    }

    public function getLogDir()
    {
        return sys_get_temp_dir().DIRECTORY_SEPARATOR.'logs'.spl_object_hash($this);
    }
}