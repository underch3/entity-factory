<?php

namespace lkovace18\EntityFactoryBundle\Tests\Factory\ConfigProvider;

use lkovace18\EntityFactoryBundle\Factory\ConfigProvider\ConfigLoader;
use lkovace18\EntityFactoryBundle\Factory\ConfigProvider\YamlConfigProvider;

class YamlConfigProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $config;

    public function setUp()
    {
        $loader = new ConfigLoader([__DIR__]);
        $configProvider = new YamlConfigProvider($loader);
        $this->config = $configProvider->getConfig();
    }

    /** @test */
    public function it_allows_string_values_in_double_quotes()
    {
        $this->assertArraySubset(['quotedString' => "'dubleQuotedString'"], $this->config['Namespace\Class']);
    }

    /** @test */
    public function it_allows_string_values_in_single_quotes()
    {
        $this->assertArraySubset(['singleQuotedString' => "'singleQuotedString'"], $this->config['Namespace\Class']);
    }

    /** @test */
    public function it_keeps_quotes_in_strings()
    {
        $this->assertArraySubset(['keepQuotes' => "faker.randomElement(['foo','bar'])"],
            $this->config['Namespace\Class']);
    }

    /** @test */
    public function it_allows_booleans()
    {
        $this->assertArraySubset(['itIsBoolean' => true], $this->config['Namespace\Class']);
    }

    /** @test */
    public function it_allows_numbers()
    {
        $this->assertArraySubset(['itIsNumber' => 12], $this->config['Namespace\Class']);
    }

    /** @test */
    public function it_allows_arrays()
    {
        $this->assertArraySubset(['ItIsArray' => ["'foo'", "'bar'"]], $this->config['Namespace\Class']);
    }
}
