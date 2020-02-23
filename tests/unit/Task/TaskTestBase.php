<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Tests\Unit\Task;

use Codeception\Test\Unit;
use League\Container\Container as LeagueContainer;
use Robo\Collection\CollectionBuilder;
use Robo\Config\Config;
use Robo\Robo;
use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyProcess;
use Sweetchuck\Robo\Drush\Test\Helper\Dummy\DummyProcessHelper;
use Sweetchuck\Robo\Drush\Test\Helper\Dummy\DummyTaskBuilder;
use Symfony\Component\Console\Application as SymfonyApplication;
use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyOutput;
use Symfony\Component\ErrorHandler\BufferingLogger;

abstract class TaskTestBase extends Unit
{
    /**
     * @var \League\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var \Robo\Config\Config
     */
    protected $config;

    /**
     * @var \Robo\Collection\CollectionBuilder
     */
    protected $builder;

    /**
     * @var \Sweetchuck\Robo\Drush\Test\UnitTester
     */
    protected $tester;

    /**
     * @var \Sweetchuck\Robo\Drush\Task\CliTaskBase
     */
    protected $task;

    /**
     * @var \Sweetchuck\Robo\Drush\Test\Helper\Dummy\DummyTaskBuilder
     */
    protected $taskBuilder;

    /**
     * @inheritdoc
     */
    public function _before()
    {
        parent::_before();

        Robo::unsetContainer();
        DummyProcess::reset();

        $this->container = new LeagueContainer();
        $application = new SymfonyApplication('Sweetchuck - Robo Drush', '1.0.0');
        $application->getHelperSet()->set(new DummyProcessHelper(), 'process');
        $this->config = new Config();
        $input = null;
        $output = new DummyOutput([
            'verbosity' => DummyOutput::VERBOSITY_DEBUG,
        ]);

        $this->container->add('container', $this->container);

        Robo::configureContainer($this->container, $application, $this->config, $input, $output);
        $this->container->share('logger', BufferingLogger::class);

        $this->builder = CollectionBuilder::create($this->container, null);
        $this->taskBuilder = new DummyTaskBuilder();
        $this->taskBuilder->setContainer($this->container);
        $this->taskBuilder->setBuilder($this->builder);

        $this->initTask();
    }

    /**
     * @return $this
     */
    protected function initTask()
    {
        $this->task = $this->taskBuilder->taskDrush();

        return $this;
    }

    abstract public function casesRunSuccess(): array;

    /**
     * @dataProvider casesRunSuccess
     */
    public function testRunSuccess(array $expected, array $processProphecy, array $options = [])
    {
        $instanceIndex = count(DummyProcess::$instances);
        DummyProcess::$prophecy[$instanceIndex] = $processProphecy;

        $result = $this->task
            ->setOptions($options)
            ->run();

        if (array_key_exists('exitCode', $expected)) {
            $this->tester->assertSame($expected['exitCode'], $result->getExitCode());
        }

        if (array_key_exists('message', $expected)) {
            $this->tester->assertSame($expected['message'], $result->getMessage());
        }

        if (array_key_exists('assets', $expected)) {
            $assets = $result->getData();
            foreach ($expected['assets'] as $assetName => $assetValue) {
                $this->tester->assertArrayHasKey($assetName, $assets);
                $this->tester->assertSame($assetValue, $assets[$assetName]);
            }
        }
    }
}
