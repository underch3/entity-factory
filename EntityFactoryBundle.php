<?php

namespace lkovace18\EntityFactoryBundle;

use lkovace18\EntityFactoryBundle\DependencyInjection\EntityFactoryExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EntityFactoryBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new EntityFactoryExtension();
        }

        return $this->extension;
    }
}
