<?php

namespace BiteCodes\TaxonomyBundle\Test;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class AppKernel extends Kernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function registerBundles()
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \BiteCodes\TaxonomyBundle\BiteCodesTaxonomyBundle(),
        ];

        return $bundles;
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $confDir = $this->getProjectDir() . '/tests/';

        $loader->load($confDir . 'services.yml');

        $c->loadFromExtension('framework', [
            'secret' => 'test_secret',
            'test'   => true,
        ]);

        $c->loadFromExtension('doctrine', [
            'dbal' => [
                'driver'    => 'pdo_sqlite',
                'memory'    => true,
                'profiling' => false,
                'logging'   => false,
            ],
            'orm'  => [
                'auto_generate_proxy_classes' => true,
                'entity_managers'             => [
                    'default' => [
                        'mappings' => [
                            'TestEntities' => [
                                'is_bundle' => false,
                                'type' => 'annotation',
                                'dir' => '%kernel.project_dir%/tests/TestEntity',
                                'prefix' => 'BiteCodes\TaxonomyBundle\Test\TestEntity',
                            ],
                            'BiteCodesTaxonomyBundle' => [],
                        ],
                    ],
                ],
            ],
        ]);

        $ids = $c->getServiceIds();
    }
}
