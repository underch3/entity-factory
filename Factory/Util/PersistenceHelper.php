<?php

namespace lkovace18\EntityFactoryBundle\Factory\Util;

use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class PersistenceHelper
{
    /** @var PropertyAccessor */
    protected $accessor;

    /** @var EntityManager */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->accessor = new PropertyAccessor();
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function persist($entity)
    {
//        $this->em->clear();

        $this->em->persist($entity);

        $this->persistAllAssociations($entity);

        $this->em->flush();

    }

    private function persistAllAssociations($entity)
    {
        $meta = $this->em->getClassMetadata(get_class($entity));

        foreach ($meta->getAssociationMappings() as $mapping) {

            $child = $this->accessor->getValue($entity, $mapping['fieldName']);

            if (null === $child) {
                continue;
            }

            if ($this->isCollection($mapping)) {
                $this->persistCollection($child);
            } else {
                $this->persistEntity($child);
            }
        }

    }

    private function isCollection($mapping): bool
    {
        return in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_MANY, ClassMetadataInfo::MANY_TO_MANY]);
    }

    private function persistCollection($collection)
    {
        foreach ($collection as $entity) {
            $this->persistEntity($entity);
        }
    }

    private function persistEntity($entity)
    {
        if ($this->isEntity($entity)) {
            if ( ! $this->em->contains($entity)) {
                $this->em->persist($entity);
                $this->persistAllAssociations($entity);
            }
        }
    }

    public function isEntity($class): bool
    {
        if (is_object($class)) {
            $class = ($class instanceof Proxy)
                ? get_parent_class($class)
                : get_class($class);
        }

        return ! $this->em->getMetadataFactory()->isTransient($class);
    }
}
