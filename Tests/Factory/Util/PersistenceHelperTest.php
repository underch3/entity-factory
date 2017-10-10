<?php


namespace lkovace18\EntityFactoryBundle\Tests\Factory\Util;

use lkovace18\EntityFactoryBundle\Factory\Util\PersistenceHelper;
use lkovace18\EntityFactoryBundle\Tests\Dummy\TestCase;
use lkovace18\EntityFactoryBundle\Tests\Dummy\TestEntity\App;
use lkovace18\EntityFactoryBundle\Tests\Dummy\TestEntity\Phone;

/**
 * Class PersistanceHelperTest
 *
 * @package lkovace18\EntityFactoryBundle\Tests\Factory\Util
 */
class PersistenceHelperTest extends TestCase
{
    /**
     * @var PersistenceHelper
     */
    protected $persistenceHelper;

    public function setUp()
    {
        parent::setUp();

        $this->persistenceHelper = new PersistenceHelper($this->em);
    }

    /** @test */
    public function it_persists_associated_entities_without_cascade_persist()
    {
        $app = new App();
        $app->setTitle('FlappyBird');

        $phone = new Phone();
        $phone->setNumber('+385913707555');
        $phone->addApp($app);

        $this->persistenceHelper->persist($phone);

        $this->assertEquals('+385913707555', $phone->getNumber());
        $this->assertEquals(1, $phone->getApps()->count());

    }

    /** @test */
    public function it_handles_non_existing_associations()
    {
        $phone = new Phone();
        $phone->setNumber('+385913707555');

        $this->persistenceHelper->persist($phone);

        $this->assertEquals('+385913707555', $phone->getNumber());
        $this->assertEmpty($phone->getApps());
    }
}
