<?php

namespace lkovace18\EntityFactoryBundle\Tests\DependencyInjection;

use lkovace18\EntityFactoryBundle\DependencyInjection\EntityFactoryExtension;
use lkovace18\EntityFactoryBundle\EntityFactoryBundle;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class EntityFactoryExtensionTest extends AbstractExtensionTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setParameter('kernel.bundles', [
            EntityFactoryBundle::class,
        ]);
    }

    /** @test */
    public function default_values_are_set()
    {
        $this->load();

        $this->assertServiceArgumentExists('entity_factory.config_provider.config_loader', 0);
        $this->assertServiceArgumentExists('entity_factory.data_provider.faker_data_provider', 0, 'en_US');
    }

    protected function assertServiceArgumentExists($service, $index, $value = null)
    {
        if ($value) {
            $this->assertContainerBuilderHasServiceDefinitionWithArgument($service, $index, $value);
        } else {
            $this->assertContainerBuilderHasServiceDefinitionWithArgument($service, $index);
        }
    }

    /** @test */
    public function locale_can_be_changed()
    {
        $this->load([
            'locale' => 'de_DE',
        ]);

        $this->assertServiceArgumentExists('entity_factory.data_provider.faker_data_provider', 0, 'de_DE');
    }

    protected function getContainerExtensions()
    {
        return [
            new EntityFactoryExtension(),
        ];
    }
}
