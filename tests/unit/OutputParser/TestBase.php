<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Tests\Unit\OutputParser;

use Codeception\Test\Unit;
use League\Container\Container as LeagueContainer;
use Robo\Collection\CollectionBuilder;
use Robo\Config\Config;
use Robo\Robo;
use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyProcess;
use Sweetchuck\Robo\Drush\Tests\Helper\Dummy\DummyProcessHelper;
use Sweetchuck\Robo\Drush\Tests\Helper\Dummy\DummyTaskBuilder;
use Symfony\Component\Console\Application as SymfonyApplication;
use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyOutput;
use Symfony\Component\ErrorHandler\BufferingLogger;

abstract class TestBase extends Unit
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
     * @var \Sweetchuck\Robo\Drush\Tests\UnitTester
     */
    protected $tester;

    /**
     * @var \Sweetchuck\Robo\Drush\Task\CliTaskBase
     */
    protected $task;

    /**
     * @var \Sweetchuck\Robo\Drush\Tests\Helper\Dummy\DummyTaskBuilder
     */
    protected $taskBuilder;

    /**
     * @var string
     */
    protected $parserClass = '';

    /**
     * @var \Sweetchuck\Robo\Drush\OutputParserInterface
     */
    protected $parser;

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
        $this->initParser();
    }

    /**
     * @return $this
     */
    protected function initTask()
    {
        $this->task = $this->taskBuilder->taskDrush();

        return $this;
    }

    /**
     * @return $this
     */
    protected function initParser()
    {
        $this->parser = new $this->parserClass();

        return $this;
    }

    abstract public function casesParse(): array;

    /**
     * @dataProvider casesParse
     */
    public function testParse(array $expected, array $taskOptions, int $exitCode, string $stdOutput, string $stdError)
    {
        $this->task->setOptions($taskOptions);
        $actual = $this->parser->parse($this->task, $exitCode, $stdOutput, $stdError);

        $this->tester->assertSame($expected, $actual);
    }
}
