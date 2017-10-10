<?php

namespace lkovace18\EntityFactoryBundle\Factory\DataProvider;

interface DataProviderInterface
{
    public function getCallableName();

    public function getProviderInstance();

    public function getProviderCallables();

    public function getIntegerDefault();

    public function getStringDefault();

    public function getDateDefault();

    public function getBooleanDefault();
}
