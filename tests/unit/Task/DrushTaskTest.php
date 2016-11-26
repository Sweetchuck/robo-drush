<?php

namespace Cheppers\Robo\Drush\Tests\Unit\Task;

use Cheppers\AssetJar\AssetJar;
use Cheppers\Robo\Drush\Task\DrushTask;
use Codeception\Test\Unit;
use Codeception\Util\Stub;

class DrushTaskTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function casesGetCommand(): array
    {
        return [
            'empty' => [
                'drush',
                [],
                [],
                [],
            ],
            'c:workingDirectory' => [
                "cd 'foo/bar' && drush",
                ['workingDirectory' => 'foo/bar'],
                [],
                [],
            ],
            'c:phpExecutable' => [
                "php7 'drush'",
                ['phpExecutable' => 'php7'],
                [],
                [],
            ],
            'c:drushExecutable' => [
                'my-drush',
                ['drushExecutable' => 'my-drush'],
                [],
                [],
            ],
            'o:format o:version' => [
                "drush --format='json' --version --yes",
                [],
                [
                    'format' => 'json',
                    'version' => true,
                    'yes' => true,
                ],
                [],
            ],
            'n:my-command' => [
                "drush my-command",
                ['cmdName' => 'my-command'],
                [],
                [],
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
        ];
    }

    /**
     * @dataProvider casesGetCommand
     */
    public function testGetCommand(string $expected, array $config, array $options, array $arguments)
    {
        $config += ['drushExecutable' => 'drush'];

        $task = new DrushTask($config, $options, $arguments);
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

    /**
     * @return array
     */
    public function casesRun()
    {
        return [
            'drush --version' => [
                'Drush Version   :  8.4.2',
                ['result' => '8.4.2'],
                ['assetJar' => new AssetJar()],
                ['version' => true],
                [],
            ],
            'drush --version --format=json' => [
                json_encode('8.4.2'),
                ['result' => '8.4.2'],
                ['assetJar' => new AssetJar()],
                ['version' => true, 'format' => 'json'],
                [],
            ],
            'drush --version --format=var_export' => [
                '$variables["Drush Version"] = \'8.4.2\';',
                ['result' => '8.4.2'],
                ['assetJar' => new AssetJar()],
                ['version' => true, 'format' => 'var_export'],
                [],
            ],
            'drush --version --format=string' => [
                '8.4.2',
                ['result' => '8.4.2'],
                ['assetJar' => new AssetJar()],
                ['version' => true, 'format' => 'string'],
                [],
            ],
            'drush --version --format=yaml' => [
                '8.4.2',
                ['result' => '8.4.2'],
                ['assetJar' => new AssetJar()],
                ['version' => true, 'format' => 'yaml'],
                [],
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
                ['version' => true, 'format' => 'yaml'],
                [],
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
                ['version' => true, 'format' => 'json'],
                [],
            ],
        ];
    }

    /**
     * @dataProvider casesRun
     */
    public function testRun(
        string $expectedStdOutput,
        array $expectedResultData,
        array $config,
        array $options,
        array $arguments
    ) {
        $container = \Robo\Robo::createDefaultContainer();
        \Robo\Robo::setContainer($container);

        $mainStdOutput = new \Helper\Dummy\Output();

        $config += [
            'workingDirectory' => '.',
            'assetJarMapping' => ['result' => ['drush', 'result']],
        ];

        /** @var \Cheppers\Robo\Drush\Task\DrushTask $task */
        $task = Stub::construct(
            DrushTask::class,
            [$config, $options, $arguments],
            [
                'processClass' => \Helper\Dummy\Process::class,
            ]
        );

        $task->setLogger($container->get('logger'));
        $task->setOutput($mainStdOutput);

        $processIndex = count(\Helper\Dummy\Process::$instances);

        \Helper\Dummy\Process::$prophecy[$processIndex] = [
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
}
