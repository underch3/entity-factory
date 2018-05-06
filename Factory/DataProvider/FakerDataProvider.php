<?php

namespace lkovace18\EntityFactoryBundle\Factory\DataProvider;

use Faker\Factory;
use Faker\Generator;

class FakerDataProvider implements DataProviderInterface
{
    const REGEX_PROPS = '/(?<=\$)\w*.*[^\)]$/m';
    const REGEX_METHODS = '/\w+(?=\()/';

    /** @var string */
    private $locale;

    public function __construct($locale = Factory::DEFAULT_LOCALE)
    {
        $this->locale = $locale;
    }

    /**
     * The string under which faker will be registered
     * as a variable in expression language
     */
    public function getCallableName(): string
    {
        return 'faker';
    }

    /**
     * Return all callable methods and properties of the library
     */
    public function getProviderCallables(): array
    {
        // @todo check if all methods are supported for all locales
        $reflection = new \ReflectionClass($this->getProviderInstance());
        $doc = $reflection->getDocComment();
        preg_match_all(self::REGEX_PROPS, $doc, $properties);
        preg_match_all(self::REGEX_METHODS, $doc, $methods);

        return array_merge($properties[0], $methods[0]);
    }

    public function getProviderInstance(): Generator
    {
        $faker = new Factory();

        return $faker->create($this->locale);
    }

    public function getIntegerDefault(): string
    {
        return 'randomNumber';
    }

    public function getSmallIntegerDefault(): string
    {
        return 'numberBetween(0, 9)';
    }

    public function getFloatDefault(): string
    {
        return 'randomFloat';
    }

    public function getLongStringDefault(): string
    {
        return 'sentence';
    }

    public function getStringDefault(): string
    {
        return 'word';
    }

    public function getDateDefault(): string
    {
        return 'dateTimeBetween';
    }

    public function lexifyString($times): string
    {
        $wildcards = str_repeat("?", $times);

        return 'lexify("' . $wildcards . '")';
    }

    public function getBooleanDefault(): string
    {
        return 'boolean';
    }
}
