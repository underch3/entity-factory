<?php

namespace lkovace18\EntityFactoryBundle\Tests\Command;

use FilesystemIterator;
use lkovace18\EntityFactoryBundle\Command\EntityFactoryGenerateDefinitionsCommand;
use lkovace18\EntityFactoryBundle\Tests\Dummy\app\AppKernel;
use lkovace18\EntityFactoryBundle\Tests\Dummy\TestCase;
use lkovace18\EntityFactoryBundle\Tests\Dummy\TestEntity\Address;
use lkovace18\EntityFactoryBundle\Tests\Dummy\TestEntity\User;
use org\bovigo\vfs\vfsStream;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Yaml\Yaml;

class EntityFactoryGenerateDefinitionsCommandTest extends TestCase
{
    /** @test */
    public function it_generates_config_files()
    {
        $kernel = new AppKernel('test', true);
        $kernel->boot();
        $kernel->getContainer()->set('doctrine.orm.default_entity_manager', $this->em);

        $configGenerator = $kernel->getContainer()->get('entity_factory.config_provider.config_generator');

        $cmd = $this->getMockBuilder(EntityFactoryGenerateDefinitionsCommand::class)
            ->setConstructorArgs([
                $configGenerator,
                $kernel,
            ])
            ->setMethods(['getDirectory'])
            ->getMock()
        ;

        $root = vfsStream::setup();

        $cmd
            ->method('getDirectory')
            ->willReturn($root->url() . '/entity_factory/')
        ;

        $application = new Application($kernel);
        $application->add($cmd);

        $commandTester = new CommandTester($cmd);
        $commandTester->execute(['command' => $cmd->getName()]);

        $fi = new FilesystemIterator($root->url() . '/entity_factory', FilesystemIterator::SKIP_DOTS);

        $this->assertEquals(11, iterator_count($fi));
        $this->assertTrue(file_exists($root->url() . '/entity_factory/Address.yml'));

        $addressConfig = Yaml::parse(file_get_contents($root->url() . '/entity_factory/Address.yml'));

        $this->assertTrue(key($addressConfig) === Address::class);
        $this->assertTrue(isset($addressConfig[Address::class]['street']));

        $userConfig = Yaml::parse(file_get_contents($root->url() . '/entity_factory/User.yml'));

        $this->assertTrue(isset($userConfig[User::class]['address']));
        $this->assertEquals(Address::class, $userConfig[User::class]['address']);
    }

    /** @test */
    public function it_returns_a_config_directory_for_a_bundle()
    {
        $bundle = $this->getMockForAbstractClass(Bundle::class, [], '', true, true, true, ['getPath']);
        $bundle
            ->expects($this->once())
            ->method('getPath')
            ->willReturn('/some/path')
        ;

        $command = $this->getMockBuilder(EntityFactoryGenerateDefinitionsCommand::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $refl = new \ReflectionClass($command);
        $method = $refl->getMethod('getDirectory');
        $method->setAccessible(true);
        $dir = $method->invokeArgs($command, [$bundle]);

        $this->assertEquals('/some/path/Resources/EntityDefinitions/', $dir);
    }
}
