<?php

namespace lkovace18\EntityFactoryBundle\Factory;

use Doctrine\ORM\EntityManager;
use lkovace18\EntityFactoryBundle\Factory\EntityBuilder\EntityBuilder;
use lkovace18\EntityFactoryBundle\Factory\Util\PersistenceHelper;
use lkovace18\EntityFactoryBundle\Factory\Util\ValueFactory;

class Factory
{
    /** var int */
    protected $times = 1;

    /** var EntityBuilder */
    private $entityBuilder;

    /** var ValueFactory */
    private $valueFactory;

    /** var PersistenceHelper */
    private $persistenceHelper;

    public function __construct(
        EntityBuilder $entityBuilder,
        ValueFactory $valueFactory,
        PersistenceHelper $persistenceHelper
    ) {
        $this->entityBuilder = $entityBuilder;
        $this->valueFactory = $valueFactory;
        $this->persistenceHelper = $persistenceHelper;
    }

    public function create($entity, array $params = [], \Closure $callback = null)
    {
        $result = $this->make($entity, $params, $callback);

        if (is_array($result)) {
            foreach ($result as $entity) {
                $this->persistenceHelper->persist($entity);
            }
        } else {
            $this->persistenceHelper->persist($result);
        }

        return $result;
    }

    public function make($entity, array $params = [], \Closure $callback = null)
    {
        $result = [];
        $isSingular = $this->times == 1;

        $dataSet = $this->values($entity, $params);

        if ($isSingular) {
            $dataSet = [$dataSet];
        }

        foreach ($dataSet as $data) {
            $result[] = $this->entityBuilder->createEntity($entity, $data, $callback);
        }

        return count($result) > 1 ? $result : array_pop($result);
    }

    public function values($entity, array $params = []): array
    {
        $result = [];
        $loops = $this->times;
        $this->times = 1;

        for ($i = 1; $i <= $loops; $i++) {
            $fakeValues = $this->valueFactory->getAllValues($entity);

            $result[] = array_merge($fakeValues, $params);
        }

        return count($result) > 1 ? $result : array_pop($result);
    }

    public function times($times): self
    {
        $this->times = $times;

        return $this;
    }

    public function setEntityManager(EntityManager $em): self
    {
        $this->persistenceHelper->setEntityManager($em);

        return $this;
    }
}
