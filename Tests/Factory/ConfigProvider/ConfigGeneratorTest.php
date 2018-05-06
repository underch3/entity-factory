<?php


namespace lkovace18\EntityFactoryBundle\Tests\Factory\ConfigProvider;

use lkovace18\EntityFactoryBundle\Factory\ConfigProvider\ConfigGenerator;
use lkovace18\EntityFactoryBundle\Factory\DataProvider\FakerDataProvider;
use lkovace18\EntityFactoryBundle\Factory\Util\DataGuesser;
use lkovace18\EntityFactoryBundle\Tests\Dummy\app\AppKernel;
use lkovace18\EntityFactoryBundle\Tests\Dummy\TestCase;
use lkovace18\EntityFactoryBundle\Tests\Dummy\TestEntity\Address;
use lkovace18\EntityFactoryBundle\Tests\Dummy\TestEntity\Hobby;

class ConfigGeneratorTest extends TestCase
{
    /**
     * @var AppKernel
     */
    protected $kernel;

    /**
     * @var ConfigGenerator
     */
    protected $generator;

    public function setUp()
    {
        parent::setUp();

        $this->kernel = new AppKernel('test', true);
        $this->kernel->boot();

        $dataProvider = new FakerDataProvider();
        $guesser = new DataGuesser($dataProvider);
        $this->generator = new ConfigGenerator($this->em, $guesser, $this->kernel);
    }

    /** @test */
    public function it_generates_an_array_of_configs_for_all_entities()
    {
        $cmf = $this->em->getMetadataFactory();
        $metadata = $cmf->getAllMetadata();

        $configs = $this->generator->generate($metadata);

        //$this->assertEquals(11, count($configs));
        //$this->assertArrayHasKey(Hobby::class, $configs);
        //$this->assertEquals(3, count($configs[Hobby::class]));
    }

    /** @test */
    public function it_generates_an_entry_for_id_fields_that_are_not_auto_generated()
    {
        $cmf = $this->em->getMetadataFactory();
        $metadata = $cmf->getAllMetadata();

        $configs = $this->generator->generate($metadata);
        //$this->assertArrayHasKey('id', $configs[Address::class]);
    }
}
