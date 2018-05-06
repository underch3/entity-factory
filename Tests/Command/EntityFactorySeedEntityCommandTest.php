<?php

namespace lkovace18\EntityFactoryBundle\Tests\Command;

use lkovace18\EntityFactoryBundle\Command\EntityFactorySeedEntityCommand;
use lkovace18\EntityFactoryBundle\Tests\Dummy\TestCase;
use lkovace18\EntityFactoryBundle\Tests\Dummy\TestEntity\App;
use lkovace18\EntityFactoryBundle\Tests\Dummy\TestEntity\Phone;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class EntityFactorySeedEntityCommandTest extends TestCase
{
    /**
     * @var Command
     */
    protected $command;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    public function setUp()
    {
        parent::setUp();

        $application = new Application();
        $application->add(new EntityFactorySeedEntityCommand($this->factory, $this->em));

        $this->command = $application->find('factory:seed:entity');
        $this->commandTester = new CommandTester($this->command);
    }

    /** @test */
    public function it_seeds_entities()
    {
        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'entity'  => Phone::class,
        ]);

        $this->seeInDatabase(Phone::class, []);
        $this->assertContains(sprintf('%s seeded.', Phone::class), $this->commandTester->getDisplay());
    }

    /** @test */
    public function the_number_of_entites_can_be_set_via_input_option()
    {
        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'entity'  => Phone::class,
            '--times' => 5,
        ]);

        $count = $this->getDatabaseCount(Phone::class, []);
        $this->assertEquals(5, $count);
        $this->assertContains(sprintf('%s seeded.', Phone::class), $this->commandTester->getDisplay());
    }

    /** @test */
    public function the_number_of_entites_can_be_set_via_input_option_via_shortcut()
    {
        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'entity'  => Phone::class,
            '-t'      => 5,
        ]);

        $count = $this->getDatabaseCount(Phone::class, []);
        $this->assertEquals(5, $count);
        $this->assertContains(sprintf('%s seeded.', Phone::class), $this->commandTester->getDisplay());
    }

    /** @test */
    public function it_opens_a_dialog_to_select_the_entity_and_count_if_no_entity_given()
    {
        //
    }

    /**
     * @param $input
     *
     * @return resource
     */
    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}
