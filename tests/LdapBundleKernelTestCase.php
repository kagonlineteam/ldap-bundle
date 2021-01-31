<?php

namespace KAGOnlineTeam\LdapBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class LdapBundleKernelTestCase extends KernelTestCase
{
    protected static function getKernelClass()
    {
        return LdapBundleTestKernel::class;
    }

    protected static function createKernel(array $options = [])
    {
        if (null === static::$class) {
            static::$class = static::getKernelClass();
        }

        $builder = \array_key_exists('builder', $options) ? $options['builder'] : null;
        $routes = \array_key_exists('routes', $options) ? $options['routes'] : [];
        $bundles = \array_key_exists('bundles', $options) ? $options['bundles'] : [];
        $compilerPasses = \array_key_exists('compiler_passes', $options) ? $options['compiler_passes'] : [];
        $pubservices = \array_key_exists('public_serives', $options) ? $options['public_services'] : [];

        return new static::$class($builder, $routes, $bundles, $compilerPasses, $pubservices);
    }

    protected function tearDown(): void
    {
        $dir = static::$kernel->getCacheDir();
        if (sys_get_temp_dir() !== \dirname($dir)) {
            $dir = \dirname($dir);
        }

        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);

        parent::tearDown();
    }
}
