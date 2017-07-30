<?php

namespace Sweetchuck\Robo\Drush\Tests\Unit\Task;

use Sweetchuck\AssetJar\AssetJar;
use Sweetchuck\Robo\Drush\Task\DrushTask;
use Sweetchuck\Robo\Drush\Test\Helper\Dummy\Output as DummyOutput;
use Sweetchuck\Robo\Drush\Test\Helper\Dummy\Process as DummyProcess;
use Codeception\Test\Unit;
use Codeception\Util\Stub;
use Robo\Robo;
use Symfony\Component\Console\Output\OutputInterface;

class DrushTaskTest extends Unit
{
    /**
     * @var \Sweetchuck\Robo\Drush\Test\UnitTester
     */
    protected $tester;

    public function casesGetCommand(): array
    {
        return [
            'empty' => [
                'drush',
            ],
            'c:workingDirectory' => [
                "cd 'foo/bar' && drush",
                ['workingDirectory' => 'foo/bar'],
            ],
            'c:phpExecutable' => [
                "php7 'drush'",
                ['phpExecutable' => 'php7'],
            ],
            'c:drushExecutable' => [
                'my-drush',
                ['drushExecutable' => 'my-drush'],
            ],
            'g:format o:version' => [
                "drush --format='json' --version --yes",
                [],
                [
                    'format' => 'json',
                    'version' => true,
                    'yes' => true,
                ],
            ],
            'n:my-command' => [
                "drush my-command",
                ['cmdName' => 'my-command'],
            ],
            'a:list' => [
                "drush 'a' 'b'",
                [],
                [],
                ['a', 'b'],
            ],
            'a:enabled' => [
                "drush 'a' 'c'",
                [],
                [],
                ['a' => true, 'b' => false, 'c' => true],
            ],
            'cgao:enabled' => [
                "drush --yes a 'b' --foo='bar'",
                ['cmdName' => 'a'],
                ['yes' => true],
                ['b'],
                ['foo' => 'bar'],
            ],
            'rsync' => [
                "drush --root='my-dir' rsync '@from' '@to' --exclude='*.txt'",
                ['cmdName' => 'rsync'],
                ['root' => 'my-dir'],
                ['@from', '@to'],
                ['exclude' => '*.txt'],
            ],
        ];
    }

    /**
     * @dataProvider casesGetCommand
     */
    public function testGetCommand(
        string $expected,
        array $config = [],
        array $globalOptions = [],
        array $arguments = [],
        array $options = []
    ): void {
        $config += ['drushExecutable' => 'drush'];

        $task = new DrushTask($config, $globalOptions, $arguments, $options);
        $this->tester->assertEquals($expected, $task->getCommand());
    }

    public function testSetCmdName()
    {
        $task = new DrushTask('');
        try {
            $task->setCmdName('bad name');
            $this->tester->fail('Invalid Drush command name was accepted.');
        } catch (\InvalidArgumentException $e) {
            // All right.
        }
    }

    public function testSetCmdOption()
    {
        $task = new DrushTask('');
        try {
            $task->setCmdOption('bad name', 'my-value');
            $this->tester->fail('Invalid Drush option name was accepted.');
        } catch (\InvalidArgumentException $e) {
            // All right.
        }
    }

    public function casesRunSuccess(): array
    {
        return [
            'drush --version' => [
                'Drush Version   :  8.4.2',
                ['result' => '8.4.2'],
                ['assetJar' => new AssetJar()],
                ['version' => true],
            ],
            'drush --version --format=json' => [
                json_encode('8.4.2'),
                ['result' => '8.4.2'],
                ['assetJar' => new AssetJar()],
                ['version' => true],
                [],
                ['format' => 'json'],
            ],
            'drush --version --format=var_export' => [
                '$variables["Drush Version"] = \'8.4.2\';',
                ['result' => '8.4.2'],
                ['assetJar' => new AssetJar()],
                ['version' => true],
                [],
                ['format' => 'var_export'],
            ],
            'drush --version --format=string' => [
                '8.4.2',
                ['result' => '8.4.2'],
                ['assetJar' => new AssetJar()],
                ['version' => true],
                [],
                ['format' => 'string'],
            ],
            'drush --version --format=yaml' => [
                '8.4.2',
                ['result' => '8.4.2'],
                ['assetJar' => new AssetJar()],
                ['version' => true],
                [],
                ['format' => 'yaml'],
            ],
            'drush pm-list --format=yaml' => [
                json_encode([
                    'system' => [
                        'type' => 'Module',
                        'status' => 'Enabled',
                    ],
                    'bartik' => [
                        'type' => 'Theme',
                        'status' => 'Enabled',
                    ],
                    'color' => [
                        'type' => 'Module',
                        'status' => 'Not installed',
                    ],
                    'toolbar' => [
                        'type' => 'Module',
                        'status' => 'Disabled',
                    ],
                ]),
                ['result' => [
                    'system' => [
                        'type' => 'Module',
                        'status' => 'Enabled',
                        'machine_name' => 'system',
                        'machine_status' => true,
                        'machine_type' => 'module',
                    ],
                    'bartik' => [
                        'type' => 'Theme',
                        'status' => 'Enabled',
                        'machine_name' => 'bartik',
                        'machine_status' => true,
                        'machine_type' => 'theme',
                    ],
                    'color' => [
                        'type' => 'Module',
                        'status' => 'Not installed',
                        'machine_name' => 'color',
                        'machine_status' => null,
                        'machine_type' => 'module',
                    ],
                    'toolbar' => [
                        'type' => 'Module',
                        'status' => 'Disabled',
                        'machine_name' => 'toolbar',
                        'machine_status' => false,
                        'machine_type' => 'module',
                    ],
                ]],
                ['cmdName' => 'pm-list', 'assetJar' => new AssetJar()],
                ['version' => true],
                [],
                ['format' => 'yaml'],
            ],
            'drush pm-list --format=json' => [
                json_encode([
                    'system' => [
                        'type' => 'Module',
                        'status' => 'Enabled',
                    ],
                    'bartik' => [
                        'type' => 'Theme',
                        'status' => 'Enabled',
                    ],
                    'color' => [
                        'type' => 'Module',
                        'status' => 'Not installed',
                    ],
                    'toolbar' => [
                        'type' => 'Module',
                        'status' => 'Disabled',
                    ],
                ]),
                ['result' => [
                    'system' => [
                        'type' => 'Module',
                        'status' => 'Enabled',
                        'machine_name' => 'system',
                        'machine_status' => true,
                        'machine_type' => 'module',
                    ],
                    'bartik' => [
                        'type' => 'Theme',
                        'status' => 'Enabled',
                        'machine_name' => 'bartik',
                        'machine_status' => true,
                        'machine_type' => 'theme',
                    ],
                    'color' => [
                        'type' => 'Module',
                        'status' => 'Not installed',
                        'machine_name' => 'color',
                        'machine_status' => null,
                        'machine_type' => 'module',
                    ],
                    'toolbar' => [
                        'type' => 'Module',
                        'status' => 'Disabled',
                        'machine_name' => 'toolbar',
                        'machine_status' => false,
                        'machine_type' => 'module',
                    ],
                ]],
                ['cmdName' => 'pm-list', 'assetJar' => new AssetJar()],
                ['version' => true],
                [],
                ['format' => 'json'],
            ],
            'StdOutputParserBase' => [
                json_encode([
                    'foo' => ['bar' => 42],
                ]),
                [
                    'result' => [
                        'foo' => ['bar' => 42],
                    ],
                ],
                ['cmdName' => 'unknown', 'assetJar' => new AssetJar()],
                [],
                [],
                ['format' => 'json'],
            ],
        ];
    }

    /**
     * @dataProvider casesRunSuccess
     */
    public function testRunSuccess(
        string $expectedStdOutput,
        array $expectedResultData,
        array $config = [],
        array $globalOptions = [],
        array $arguments = [],
        array $options = []
    ) {
        $container = Robo::createDefaultContainer();
        Robo::setContainer($container);

        $outputConfig = [
            'verbosity' => OutputInterface::VERBOSITY_DEBUG,
            'colors' => false,
        ];
        $mainStdOutput = new DummyOutput($outputConfig);

        $config += [
            'assetJarMapping' => ['result' => ['drush', 'result']],
        ];

        /** @var \Sweetchuck\Robo\Drush\Task\DrushTask $task */
        $task = Stub::construct(
            DrushTask::class,
            [$config, $globalOptions, $arguments, $options],
            [
                'processClass' => DummyProcess::class,
            ]
        );

        $task->setLogger($container->get('logger'));
        $task->setOutput($mainStdOutput);

        $processIndex = count(DummyProcess::$instances);

        DummyProcess::$prophecy[$processIndex] = [
            'exitCode' => 0,
            'stdOutput' => $expectedStdOutput,
        ];

        $result = $task->run();

        $this->tester->assertEquals(
            $expectedStdOutput,
            $mainStdOutput->output,
            'Output equals'
        );

        $this->tester->assertEquals(
            $expectedResultData,
            $result->getData(),
            'Result::data equals'
        );

        if ($task->hasAssetJar()) {
            $this->tester->assertEquals(
                $expectedResultData['result'],
                $task->getAssetJarValue('result'),
                'AssetJar::result equals'
            );
        }
    }

    public function casesRunFail(): array
    {
        return [
            'basic' => [
                [
                    'exitCode' => 1,
                    'stdOutput' => '',
                    'stdError' => '',
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesRunFail
     */
    public function testRunFail(array $expected, array $processProphecy = []): void
    {
        $processProphecy += [
            'exitCode' => 1,
            'stdOutput' => '',
            'stdError' => '',
        ];

        $container = Robo::createDefaultContainer();
        Robo::setContainer($container);

        $outputConfig = [
            'verbosity' => OutputInterface::VERBOSITY_DEBUG,
            'colors' => false,
        ];
        $mainStdOutput = new DummyOutput($outputConfig);

         /** @var \Sweetchuck\Robo\Drush\Task\DrushTask $task */
        $task = Stub::construct(
            DrushTask::class,
            [[], [], [], []],
            [
                'processClass' => DummyProcess::class,
            ]
        );

        $task->setLogger($container->get('logger'));
        $task->setOutput($mainStdOutput);

        $processIndex = count(DummyProcess::$instances);

        DummyProcess::$prophecy[$processIndex] = $processProphecy;

        $result = $task->run();

        if (isset($expected['exitCode'])) {
            $this->tester->assertEquals(
                $expected['exitCode'],
                $result->getExitCode(),
                'Exit code equals'
            );
        }

        if (isset($expected['stdOutput'])) {
            $this->tester->assertEquals(
                $expected['stdOutput'],
                $mainStdOutput->output,
                'StdOutput equals'
            );
        }

        if (isset($expected['stdError'])) {
            $this->tester->assertEquals(
                $expected['stdError'],
                $mainStdOutput->getErrorOutput()->output,
                'StdOutput equals'
            );
        }

        if (isset($expected['resultData'])) {
            $this->tester->assertEquals(
                $expected['resultData'],
                $result->getData(),
                'Result data equals'
            );
        }
    }
}
