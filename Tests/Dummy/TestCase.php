<?php

namespace lkovace18\EntityFactoryBundle\Tests\Dummy;

use Doctrine\ORM\EntityManager;
use lkovace18\EntityFactoryBundle\Factory\ConfigProvider\ConfigLoader;
use lkovace18\EntityFactoryBundle\Factory\ConfigProvider\YamlConfigProvider;
use lkovace18\EntityFactoryBundle\Factory\DataProvider\FakerDataProvider;
use lkovace18\EntityFactoryBundle\Factory\EntityBuilder\EntityBuilder;
use lkovace18\EntityFactoryBundle\Factory\Factory;
use lkovace18\EntityFactoryBundle\Factory\Util\PersistenceHelper;
use lkovace18\EntityFactoryBundle\Factory\Util\ValueFactory;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestDatabase
     */
    protected $testDatabase;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Factory
     */
    protected $factory;

    public function setUp()
    {
        parent::setUp();

        $here = dirname(__FILE__);

        $this->testDatabase = new TestDatabase(
            $here . '/TestEntity',
            $here . '/TestProxy',
            'lkovace18\EntityFactoryBundle\Tests\Dummy\TestEntity'
        );

        $this->em = $this->testDatabase->createEntityManager();

        $configDir = __DIR__ . '/Config';

        $loader = new ConfigLoader([$configDir]);

        $configProvider = new YamlConfigProvider($loader);
        $fakerDataProvider = new FakerDataProvider();

        $valueFactory = new ValueFactory($configProvider, [$fakerDataProvider]);

        $persistenceHelper = new PersistenceHelper($this->em);

        $entityBuilder = new EntityBuilder($this->em);
        $this->factory = new Factory($entityBuilder, $valueFactory, $persistenceHelper);
    }

    protected function seeInDatabase($entity, $criteria)
    {
        $count = $this->getDatabaseCount($entity, $criteria);

        $this->assertGreaterThan(0, $count, sprintf(
            'Unable to find row in database table [%s] that matched attributes [%s].', $entity, json_encode($criteria)
        ));

        return $this;
    }

    protected function getDatabaseCount($entity, $criteria)
    {
        $qb = $this->em
            ->createQueryBuilder()
            ->select('COUNT(e)')
            ->from($entity, 'e')
        ;

        foreach ($criteria as $field => $value) {
            $qb->andWhere("e.{$field} = :{$field}")->setParameter($field, $value);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    protected function seeNotInDatabase($entity, $criteria)
    {
        $count = $this->getDatabaseCount($entity, $criteria);

        $this->assertEquals(0, $count, sprintf(
            'Found row in database table [%s] that matched attributes [%s].', $entity, json_encode($criteria)
        ));

        return $this;
    }
}
