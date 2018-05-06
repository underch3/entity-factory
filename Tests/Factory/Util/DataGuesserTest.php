<?php


namespace lkovace18\EntityFactoryBundle\Tests\Factory\Util;


use lkovace18\EntityFactoryBundle\Factory\DataProvider\FakerDataProvider;
use lkovace18\EntityFactoryBundle\Factory\Util\DataGuesser;

class DataGuesserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DataGuesser
     */
    protected $guesser;

    public function setUp()
    {
        $dataProvider = new FakerDataProvider();
        $this->guesser = new DataGuesser($dataProvider);
    }

    /** @test */
    public function it_guesses_city_data()
    {
        $mapping = [
            'fieldName' => 'city',
            'type'      => 'randomType',
            'unique'    => false,
        ];

        $result = $this->guesser->guess($mapping);

        $this->assertEquals('faker.city', $result);
    }

    /** @test */
    public function it_detects_unique_field()
    {
        $this->markTestSkipped('Wip');
    }

    /** @test */
    public function it_skipps_detection_for_not_mandatory_fields()
    {
        $this->markTestSkipped('Wip');
    }

    /** @test */
    public function it_guesses_street_data()
    {
        $mapping = [
            'fieldName' => 'street',
            'type'      => 'randomType',
            'unique'    => false,
        ];

        $result = $this->guesser->guess($mapping);

        $this->assertContains('faker.street', $result);
    }

    /** @test */
    public function it_guesses_name_data()
    {
        $mapping = [
            'fieldName' => 'name',
            'type'      => 'randomType',
            'unique'    => false,
        ];

        $result = $this->guesser->guess($mapping);

        $this->assertEquals('faker.name', $result);
    }

    /** @test */
    public function it_uses_small_integer_data()
    {
        $this->markTestSkipped('Wip');

        $mapping1 = ['fieldName' => 'nonExistingProvider', 'type' => 'smallint'];

        $result1 = $this->guesser->guess($mapping1);


    }

    /** @test */
    public function it_uses_float_integer_data()
    {
        $this->markTestSkipped('Wip');

        $mapping1 = ['fieldName' => 'nonExistingProvider', 'type' => 'float'];
        $mapping2 = ['fieldName' => 'nonExistingProvider', 'type' => 'double'];

        $result1 = $this->guesser->guess($mapping1);
        $result2 = $this->guesser->guess($mapping2);

    }

    /** @test */
    public function it_uses_default_integer_data()
    {
        $mapping1 = [
            'fieldName' => 'nonExistingProvider',
            'type'      => 'integer',
            'unique'    => false,
        ];
        $mapping2 = [
            'fieldName' => 'nonExistingProvider',
            'type'      => 'bigint',
            'unique'    => false,
        ];


        $result1 = $this->guesser->guess($mapping1);
        $result2 = $this->guesser->guess($mapping2);


        $this->assertEquals('faker.randomNumber', $result1);
        $this->assertEquals('faker.randomNumber', $result2);
    }

    /** @test */
    public function it_uses_default_string_data()
    {
        $mapping1 = [
            'fieldName' => 'song',
            'type'      => 'string',
            'unique'    => false,
        ];
        $mapping2 = [
            'fieldName' => 'song',
            'type'      => 'text',
            'unique'    => false,
        ];

        $result1 = $this->guesser->guess($mapping1);
        $result2 = $this->guesser->guess($mapping2);

        $this->assertEquals('faker.word', $result1);
        $this->assertEquals('faker.word', $result2);
    }

    /** @test */
    public function it_uses_lexify_string_data_if_needed()
    {
        $this->markTestSkipped('Wip');
    }

    /** @test */
    public function it_uses_default_date_data()
    {
        $mapping1 = [
            'fieldName' => 'dob',
            'type'      => 'date',
            'unique'    => false,
        ];
        $mapping2 = [
            'fieldName' => 'dob',
            'type'      => 'datetime',
            'unique'    => false,
        ];

        $result1 = $this->guesser->guess($mapping1);
        $result2 = $this->guesser->guess($mapping2);

        $this->assertEquals('faker.dateTimeBetween', $result1);
        $this->assertEquals('faker.dateTimeBetween', $result2);
    }

    /** @test */
    public function it_uses_default_boolean_data()
    {
        $mapping = [
            'fieldName' => 'isChecked',
            'type' => 'boolean',
            'unique'    => false,
        ];

        $result = $this->guesser->guess($mapping);

        $this->assertEquals('faker.boolean', $result);
    }

    /** @test */
    public function it_throws_exception_for_unknown_types()
    {
        $this->markTestSkipped('Wip');

        $mapping = ['fieldName' => 'Weirdo', 'type' => 'strange'];
    }
}
