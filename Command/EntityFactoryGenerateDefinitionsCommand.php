<?php

namespace lkovace18\EntityFactoryBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use lkovace18\EntityFactoryBundle\Factory\ConfigProvider\ConfigGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Dumper;

class EntityFactoryGenerateDefinitionsCommand extends Command
{
    /** @var ConfigGenerator */
    private $generator;

    /** @var Kernel */
    private $kernel;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(ConfigGenerator $generator, Kernel $kernel, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->generator = $generator;
        $this->entityManager = $entityManager;
        $this->kernel = $kernel;
    }

    protected function configure()
    {
        $this
            ->setName('factory:generate:definitions')
            ->setDescription('It generates the factory definitions for entities.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Generating definitions for entities...');

        $cmf = $this->entityManager->getMetadataFactory();
        $metadata = $cmf->getAllMetadata();
        $dumper = new Dumper();

        $entitiesDefinition = $this->generator->generate($metadata);

        foreach ($entitiesDefinition as $entity => $definition) {

            $reflection = new \ReflectionClass($entity);

            $factoryDefinitionsDirectory = $this->getDirectory();

            $definitionFile = $factoryDefinitionsDirectory . $reflection->getShortName() . '.yml';

            if (file_exists($factoryDefinitionsDirectory) === false) {
                // @todo: fix premissions
                mkdir($factoryDefinitionsDirectory, 0777, true);
            }

            $entityDefinition = $dumper->dump([$entity => $definition], 4);

            /* check why this quotes string  */
            $entityDefinition = str_replace("'", "", $entityDefinition);

            // @todo: find out how to handle existing files with definition, merge them ?
            if (file_exists($definitionFile) === false) {
                file_put_contents($definitionFile, $entityDefinition);
                $output->writeln(sprintf('<info>Definition created for: %s </info>', $entity));
            } else {
                $output->writeln(sprintf('<info>Definition exist for: %s </info>', $entity));
            }
        }
    }

    /**
     * @return string
     */
    protected function getDirectory()
    {
        $path = '';
        if(empty($this->kernel) === false) {
            $path = $this->kernel->getRootDir();
        }

        return $path . '/Migrations/EntityDefinitions/';
    }
}
