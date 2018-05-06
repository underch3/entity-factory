<?php

namespace lkovace18\EntityFactoryBundle\Factory\Util;

use lkovace18\EntityFactoryBundle\Factory\ConfigProvider\ConfigProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ValueFactory
{
    /** var array*/
    protected $config;

    /** var ExpressionLanguage*/
    protected $language;

    /** var array*/
    protected $providerValues;

    public function __construct(ConfigProviderInterface $config, array $dataProviders)
    {
        $this->config = $config->getConfig();

        $this->language = new ExpressionLanguage();
        $this->providerValues = $this->getDataProviderValues($dataProviders);
    }

    protected function getDataProviderValues(array $dataProviders): array
    {
        $providerValues = [];

        foreach ($dataProviders as $provider) {
            $providerValues[$provider->getCallableName()] = $provider->getProviderInstance();
        }

        return $providerValues;
    }

    /* --------------------------------------------------------
       |  Return all values for an Entity
      -------------------------------------------------------- */
    public function getAllValues($entity, $parent = null): array
    {
        $data = [];

        if (isset($this->config[$entity]) === false) {
            return $data;
        }

        foreach ($this->config[$entity] as $field => $expression) {

            if ($parent == $expression) {
                continue;
            }

            if (is_string($expression) && class_exists($expression)) {
                $data[$field] = $this->getAllValues($expression, $entity);
            } else {
                $data[$field] = $this->getValue($entity, $field);
            }
        }

        return $data;
    }

    /* --------------------------------------------------------
       |  Return the value for an field of an entity
      -------------------------------------------------------- */
    public function getValue($entity, $field)
    {
        $data = $this->config[$entity][$field];

        if (is_string($data)) {
            $data = $this->evaluateExpression($data);
        } elseif (is_array($data)) {
            $data = $this->evaluateExpressionsInArray($data);
        }

        return $data;
    }

    protected function evaluateExpression($expression)
    {
        return $this->language->evaluate($expression, $this->providerValues);
    }

    protected function evaluateExpressionsInArray($data)
    {
        array_walk_recursive($data, function (&$value) {
            $value = $this->evaluateExpression($value);
        });

        return $data;
    }
}
