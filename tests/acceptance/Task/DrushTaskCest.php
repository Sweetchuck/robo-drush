<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Tests\Acceptance\Task;

use Sweetchuck\Robo\Drush\Test\AcceptanceTester;
use Sweetchuck\Robo\Drush\Test\Helper\RoboFiles\DrushRoboFile;
use Symfony\Component\Yaml\Yaml;

class DrushTaskCest
{
    protected string $drushVersion = '10.6.0';

    protected string $drushExecutable = 'bin/drush';

    public function drushVersion(AcceptanceTester $tester): void
    {
        $id = 'version';

        $taskOptions = [
            'drushExecutable' => $this->drushExecutable,
            'cmdName' => 'version',
        ];
        $tester->runRoboTask($id, DrushRoboFile::class, 'drush', json_encode($taskOptions));

        $exitCode = $tester->getRoboTaskExitCode($id);
        $stdOutput = $tester->getRoboTaskStdOutput($id);
        $stdError = $tester->getRoboTaskStdError($id);

        $tester->assertSame(0, $exitCode, 'Exit code same');
        $tester->assertSame(
            implode(PHP_EOL, [
                " Drush version : {$this->drushVersion} ",
                '',
            ]),
            $stdOutput,
            'stdOutput same'
        );
        $tester->assertEquals(
            implode(PHP_EOL, [
                " [Drush] bin/drush version",
                "  RUN  'bin/drush' 'version'",
                "  OUT   Drush version : {$this->drushVersion} ",
                '  OUT  ',
                '  RES  Command ran successfully',
                '',
            ]),
            $stdError
        );
    }

    public function drushVersionYaml(AcceptanceTester $tester): void
    {
        $id = 'version:yaml';
        $taskOptions = [
            'drushExecutable' => $this->drushExecutable,
            'cmdName' => 'version',
            'cmdOptions' => [
                'format' => 'yaml',
            ],
        ];
        $tester->runRoboTask($id, DrushRoboFile::class, 'drush', json_encode($taskOptions));

        $exitCode = $tester->getRoboTaskExitCode($id);
        $stdOutput = $tester->getRoboTaskStdOutput($id);
        $stdError = $tester->getRoboTaskStdError($id);

        $tester->assertSame(0, $exitCode, 'Exit code same');
        $tester->assertSame(
            implode(PHP_EOL, [
                ' [Drush] bin/drush version --format=yaml',
                "  RUN  'bin/drush' 'version' '--format=yaml'",
                "  OUT  drush-version: {$this->drushVersion}",
                '  OUT  ',
                '  OUT  ',
                '  RES  Command ran successfully',
                '',
            ]),
            $stdError,
            'stdError same'
        );

        $tester->assertSame(
            [
                'drush-version' => $this->drushVersion
            ],
            Yaml::parse($stdOutput),
            'stdOutput same'
        );
    }
}
