<?php


namespace lkovace18\EntityFactoryBundle\Tests\Dummy\app;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use lkovace18\EntityFactoryBundle\EntityFactoryBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new EntityFactoryBundle(),
        ];

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config.yml');
    }
}
