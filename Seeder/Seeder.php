<?php

namespace lkovace18\EntityFactoryBundle\Seeder;

use lkovace18\EntityFactoryBundle\Factory\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class Seeder
{
    use ContainerAwareTrait;

    /**
     * @var Factory
     */
    protected $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    abstract public function run();
}
