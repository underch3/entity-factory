<?php

namespace lkovace18\EntityFactoryBundle\Command;

use lkovace18\EntityFactoryBundle\Factory\ConfigProvider\ConfigGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Dumper;

class EntityFactoryGenerateDefinitionsCommand extends Command
{
    /**
     * @var ConfigGenerator
     */
    private $generator;

    /**
     * @var Kernel
     */
    private $kernel;

    public function __construct(ConfigGenerator $generator, Kernel $kernel)
    {
        parent::__construct();

        $this->generator = $generator;
        $this->kernel = $kernel;
    }

    protected function configure()
    {
        $this
            ->setName('factory:entity:generate:definitions')
            ->setDescription('It generates the factory definitions for entities.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Generating definitions for entities...');

        $dumper = new Dumper();

        $bundles = $this->kernel->getBundles();
        $entitiesDefinition = $this->generator->generate();

        /** @var Bundle $bundle */
        foreach ($bundles as $bundle) {

            foreach ($entitiesDefinition as $entity => $definition) {

                /* find entities for current bundle */
                if (strpos($entity, $bundle->getNamespace()) === false) {
                    continue;
                }

                $reflection = new \ReflectionClass($entity);
                $factoryDefinitionsDirectory = $this->getDirectory($bundle);

                $definitionFile = $factoryDefinitionsDirectory . $reflection->getShortName() . '.yml';

                if ( ! file_exists($factoryDefinitionsDirectory)) {

                    // @todo: fix premissions
                    mkdir($factoryDefinitionsDirectory, 0777, true);
                }

                $entityDefinition = $dumper->dump([$entity => $definition], 4);

                /* check why this quotes string  */
                $entityDefinition = str_replace("'", "", $entityDefinition);

                // @todo: find out how to handle existing files with definition
                // merge them ?
                if ( ! file_exists($definitionFile)) {
                    file_put_contents($definitionFile, $entityDefinition);
                    $output->writeln(sprintf('<info>Definition created for: %s </info>', $entity));
                } else {
                    $output->writeln(sprintf('<info>Definition exist for: %s </info>', $entity));
                }


            }
        }
    }

    /**
     * Find Entity definitions folder for bundle
     *
     * @param Bundle $bundle
     *
     * @return string
     */
    protected function getDirectory(Bundle $bundle)
    {
        $path = $bundle->getPath();

        return $path . '/Resources/EntityDefinitions/';
    }
}
