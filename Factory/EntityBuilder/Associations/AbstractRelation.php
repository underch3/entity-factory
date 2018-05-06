<?php

namespace lkovace18\EntityFactoryBundle\Factory\EntityBuilder\Associations;

use Dflydev\DotAccessData\Data;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use lkovace18\EntityFactoryBundle\Factory\EntityBuilder\EntityBuilder;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractRelation
{
    /** @var PropertyAccessor */
    protected $accessor;

    /** @var EntityBuilder */
    protected $entityBuilder;

    /** @var string */
    protected $association;

    /** @var ClassMetadataInfo */
    protected $meta;

    /** @var object */
    protected $instance;

    /** @var Data */
    protected $params;

    public function __construct(PropertyAccessor $accessor, EntityBuilder $entityBuilder)
    {
        $this->accessor = $accessor;
        $this->entityBuilder = $entityBuilder;
    }

    public function handle($association, ClassMetadataInfo $meta, $instance, Data $params)
    {
        if ($params->get($association) === null) {
            return;
        }

        $this->association = $association;
        $this->meta = $meta;
        $this->instance = $instance;
        $this->params = $params;

        switch (true) {
            case $this->isSelfReferential():
                $this->selfReferential();
                break;
            case $this->isUniDirectional():
                $this->uniDirectional();
                break;
            case $this->isBiDirectional():
                $this->biDirectional();
                break;
        }
    }

    private function isSelfReferential()
    {
        $mapping = $this->meta->getAssociationMapping($this->association);

        return $mapping['targetEntity'] == $mapping['sourceEntity'];
    }

    abstract protected function selfReferential();

    private function isUniDirectional()
    {
        $mapping = $this->meta->getAssociationMapping($this->association);

        return null === $mapping['mappedBy'] && null === $mapping['inversedBy'];
    }

    abstract protected function uniDirectional();

    private function isBiDirectional()
    {
        return $this->isUniDirectional() === false && $this->isSelfReferential() === false;
    }

    abstract protected function biDirectional();
}
