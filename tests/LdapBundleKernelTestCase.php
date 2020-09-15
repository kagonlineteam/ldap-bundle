<?php

namespace KAGOnlineTeam\LdapBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * 
 */
abstract class LdapBundleKernelTestCase extends KernelTestCase
{
    protected static function getKernelClass()
    {
        return LdapBundleTestKernel::class;
    }

    /**
     * 
     */
    protected static function createKernel(array $options = [])
    {
        if (null === static::$class) {
            static::$class = static::getKernelClass();
        }

        $builder = key_exists('builder', $options) ? $options['builder'] : null;
        $routes = key_exists('routes', $options) ? $options['routes'] : [];
        $bundles = key_exists('bundles', $options) ? $options['bundles'] : [];
        
        return new static::$class($builder, $routes, $bundles);
    }
}