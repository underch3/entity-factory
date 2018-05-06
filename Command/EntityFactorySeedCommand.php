<?php

namespace lkovace18\EntityFactoryBundle\Command;

use lkovace18\EntityFactoryBundle\Factory\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EntityFactorySeedCommand extends Command
{
    /** @var Factory */
    private $factory;

    /** @var string */
    private $directory;

    public function __construct(Factory $factory, string $directory)
    {
        parent::__construct();

        $this->factory = $factory;
        $this->directory = $directory;
    }

    protected function configure()
    {
        $this
            ->setName('factory:seed')
            ->setDescription('Seeds database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($handle = opendir($this->directory)) {
            while (false !== ($entry = readdir($handle))) {
                $path = pathinfo($entry);
                if (substr($path['basename'], -10, 10) == 'Seeder.php') {
                    require_once($this->directory . $entry);
                    if (class_exists($path['filename'])) {
                        $class = $path['filename'];
                        $seeder = new $class($this->factory);
                        $seeder->run();
                    }
                }
            }
        }

    }
}
