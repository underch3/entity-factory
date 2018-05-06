<?php


namespace lkovace18\EntityFactoryBundle\Factory\Util;

use lkovace18\EntityFactoryBundle\Factory\DataProvider\FakerDataProvider;

class DataGuesser
{
    /** @var FakerDataProvider */
    protected $dataProvider;

    public function __construct(FakerDataProvider $faker)
    {
        // @todo exchange for interface or array of providers
        $this->dataProvider = $faker;
    }

    /* --------------------------------------------------------
       |  Return an expression for the provider
      -------------------------------------------------------- */
    public function guess($mapping): string
    {
        $method = $this->guessByProviderCallables($mapping);

        if (empty($method) === true) {
            $method = $this->getByType($mapping);
        }

        if (isset($mapping['unique']) && $mapping['unique'] === true) {
            return $this->dataProvider->getCallableName() . '.unique.' . $method;
        }

        return $this->dataProvider->getCallableName() . '.' . $method;
    }

    /* --------------------------------------------------------
       |  Return an expression for the provider
      -------------------------------------------------------- */
    protected function guessByProviderCallables($mapping): string
    {
        /* we dont want string, why date provider returns string :( */
        if ($mapping['type'] === 'date' || $mapping['type'] === 'datetime') {
            return '';
        }

        $options = [];
        $fieldName = strtolower($mapping['fieldName']);

        foreach ($this->dataProvider->getProviderCallables() as $callable) {
            if (strpos($callable, $fieldName) !== false) {

                /* temp fix for faker bug */
                if (strpos($callable, 'vat') !== false) {
                    continue;
                }
                /* ehm ... */
                if ($callable === 'postcode' && $fieldName !== 'postcode') {
                    continue;
                }

                $options[] = $callable;
            }
        }

        foreach ($options as $option) {
            if (strcasecmp($option, $fieldName) == 0) {
                return $option;
            }
        }

        if (empty($options) === false) {
            return $options[0];
        }

        return '';
    }

    /* --------------------------------------------------------
       |  Get some defaults
      -------------------------------------------------------- */
    protected function getByType($mapping)
    {
        switch ($mapping['type']) {
            case 'integer':
            case 'bigint':
                $method = $this->dataProvider->getIntegerDefault();
                if (empty($mapping['length']) === false) {
                    var_dump($mapping);
                    die;
                }
                break;
            case 'smallint':
                $method = $this->dataProvider->getSmallIntegerDefault();
                if (empty($mapping['length']) === false) {
                    var_dump($mapping);
                    die;
                }
                break;
            case 'decimal':
            case 'float':
                $method = $this->dataProvider->getFloatDefault();
                if (empty($mapping['scale']) === false && empty($mapping['precision'] === false)) {
                    $depth = $mapping['precision'] - $mapping['scale'];
                    $method .= "($depth)";
                }
                break;
            case 'text':
                $method = $this->dataProvider->getStringDefault();
                break;
            case 'string':
                $method = $this->dataProvider->getStringDefault();
                if (empty($mapping['length']) === false && $mapping['length'] < 50) {
                    $method = $this->dataProvider->lexifyString($mapping['length']);
                }
                break;
            case 'datetime':
            case 'date':
                $method = $this->dataProvider->getDateDefault();
                break;
            case 'boolean':
                $method = $this->dataProvider->getBooleanDefault();
                break;
            default:
                // @todo throw exception ?
                $method = '?';
        }

        return $method;
    }
}
