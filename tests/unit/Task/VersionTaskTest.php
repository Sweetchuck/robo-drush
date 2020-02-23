<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Tests\Unit\Task;

use InvalidArgumentException;
use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyProcess;
use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @covers \Sweetchuck\Robo\Drush\Task\DrushTask<extended>
 */
class VersionTaskTest extends TaskTestBase
{
    public function casesGetCommand(): array
    {
        return [
            'empty' => [
                'drush',
            ],
            'workingDirectory' => [
                "cd 'foo/bar' && drush",
                [
                    'workingDirectory' => 'foo/bar',
                ],
            ],
            'phpExecutable' => [
                'php7 drush',
                [
                    'phpExecutable' => 'php7',
                ],
            ],
            'drushExecutable' => [
                'my-drush',
                [
                    'drushExecutable' => 'my-drush',
                ],
            ],
            'phpExecutable with drushExecutable' => [
                'php7 my-drush',
                [
                    'phpExecutable' => 'php7',
                    'drushExecutable' => 'my-drush',
                ],
            ],
            'g:format o:version' => [
                "drush --format=json --version --yes",
                [
                    'cmdGlobalOptions' => [
                        'format' => 'json',
                        'version' => true,
                        'yes' => true,
                    ],
                ],
            ],
            'n:my-command' => [
                "drush my-command",
                [
                    'cmdName' => 'my-command',
                ],
            ],
            'a:list' => [
                "drush 'a' 'b'",
                [
                    'cmdArguments' => [
                        'a',
                        'b',
                    ],
                ],
            ],
            'cgao:enabled' => [
                "drush --yes a --foo=bar 'b'",
                [
                    'cmdName' => 'a',
                    'cmdGlobalOptions' => [
                        'yes' => true,
                    ],
                    'cmdOptions' => [
                        'foo' => 'bar',
                    ],
                    'cmdArguments' => [
                        'b',
                    ],
                ],
            ],
            'rsync' => [
                "drush --root=my-dir rsync --exclude=*.txt '@from' '@to'",
                [
                    'cmdName' => 'rsync',
                    'cmdGlobalOptions' => [
                        'root' => 'my-dir',
                    ],
                    'cmdOptions' => [
                        'exclude' => '*.txt',
                    ],
                    'cmdArguments' => [
                        '@from', '@to'
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesGetCommand
     */
    public function testGetCommand(
        string $expected,
        array $options = []
    ): void {
        $options += ['drushExecutable' => 'drush'];
        $this->task->setOptions($options);

        $this->tester->assertEquals($expected, $this->task->getCommand());
    }

    public function testSetCmdName()
    {
        try {
            $this->task->setCmdName('bad name');
            $this->tester->fail('Invalid Drush command name was accepted.');
        } catch (InvalidArgumentException $e) {
            // All right.
        }
    }

    public function testSetCmdOption()
    {
        try {
            $this->task->setCmdOption('bad name', 'my-value');
            $this->tester->fail('Invalid Drush option name was accepted.');
        } catch (InvalidArgumentException $e) {
            // All right.
        }
    }

    public function casesRunSuccess(): array
    {
        return [
            'drush version --format=yaml' => [
                [
                    'exitCode' => 0,
                    'assets' => [
                        'my-prefix.drush-version' => '10.1.1'
                    ],
                ],
                [
                    'exitCode' => 0,
                    'stdOutput' => 'drush-version: 10.1.1',
                    'stdError' => '',
                ],
                [
                    'assetNamePrefix' => 'my-prefix.',
                    'cmdName' => 'version',
                    'cmdOptions' => [
                        'format' => 'yaml',
                    ],
                ],
            ],
            'drush version --format=json' => [
                [
                    'exitCode' => 0,
                    'assets' => [
                        'my-prefix.drush-version' => '10.1.1'
                    ],
                ],
                [
                    'exitCode' => 0,
                    'stdOutput' => '{"drush-version": "10.1.1"}',
                    'stdError' => '',
                ],
                [
                    'assetNamePrefix' => 'my-prefix.',
                    'cmdName' => 'version',
                    'cmdOptions' => [
                        'format' => 'json',
                    ],
                ],
            ],
            'drush version --format=var_export' => [
                [
                    'exitCode' => 0,
                    'assets' => [
                        'drush-version' => '10.1.1'
                    ],
                ],
                [
                    'exitCode' => 0,
                    'stdOutput' => "array ( 'drush-version' => '10.1.1' )",
                    'stdError' => '',
                ],
                [
                    'cmdName' => 'version',
                    'cmdOptions' => [
                        'format' => 'var_export',
                    ],
                ],
            ],
            'drush version --format=string' => [
                [
                    'exitCode' => 0,
                    'assets' => [
                        'drush-version' => '10.1.1'
                    ],
                ],
                [
                    'exitCode' => 0,
                    'stdOutput' => "10.1.1\n",
                    'stdError' => '',
                ],
                [
                    'cmdName' => 'version',
                    'cmdOptions' => [
                        'format' => 'string',
                    ],
                ],
            ],
        ];
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
    public function testRunFail(array $expected, array $processProphecy = [], array $options = []): void
    {
        $processProphecy += [
            'exitCode' => 1,
            'stdOutput' => '',
            'stdError' => '',
        ];

        $instanceIndex = count(DummyProcess::$instances);
        DummyProcess::$prophecy[$instanceIndex] = $processProphecy;

        $outputConfig = [
            'verbosity' => OutputInterface::VERBOSITY_DEBUG,
            'colors' => false,
        ];
        $mainStdOutput = new DummyOutput($outputConfig);

        $result = $this->task
            ->setOutput($mainStdOutput)
            ->setOptions($options)
            ->run();

        if (isset($expected['exitCode'])) {
            $this->tester->assertSame(
                $expected['exitCode'],
                $result->getExitCode(),
                'Exit code same'
            );
        }

        if (isset($expected['stdOutput'])) {
            $this->tester->assertSame(
                $expected['stdOutput'],
                $mainStdOutput->output,
                'StdOutput same'
            );
        }

        if (isset($expected['stdError'])) {
            $this->tester->assertSame(
                $expected['stdError'] . implode(PHP_EOL, [
                    '  RUN  ""',
                    '  RES  1 Command did not run successfully',
                    '',
                ]),
                $mainStdOutput->getErrorOutput()->output,
                'StdError same'
            );
        }

        if (isset($expected['resultData'])) {
            $this->tester->assertSame(
                $expected['resultData'],
                $result->getData(),
                'Result data same'
            );
        }
    }
}
