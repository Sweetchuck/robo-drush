<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Tests\Acceptance\Task;

use Sweetchuck\Robo\Drush\Tests\AcceptanceTester;
use Sweetchuck\Robo\Drush\Tests\Helper\RoboFiles\DrushRoboFile;
use Symfony\Component\Yaml\Yaml;

class DrushTaskCest
{
    protected string $drushVersion = '10.6.2';

    protected string $drushExecutable = 'tests/_data/fixtures/project-01/vendor/bin/drush';

    public function _before()
    {
        $projectDir = codecept_data_dir('fixtures/project-01');
        if (!file_exists($projectDir)) {
            mkdir($projectDir, 0777 - umask(), true);
        }

        if (!file_exists("$projectDir/composer.json")) {
            file_put_contents(
                "$projectDir/composer.json",
                '{ "name": "sweetchuck/dummy-robo-drush-project-01" }',
            );

            exec(sprintf(
                'cd %s && composer require %s',
                escapeshellarg($projectDir),
                escapeshellarg('drush/drush:^10.0'),
            ));
        }
    }

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
                "Drush version : {$this->drushVersion} ",
                '',
            ]),
            $stdOutput,
            'stdOutput same'
        );

        $tester->assertSame(
            implode(PHP_EOL, [
                " [Drush] {$this->drushExecutable} version",
                "  RUN  '{$this->drushExecutable}' 'version'",
                "  OUT  Drush version : {$this->drushVersion} ",
                '  OUT  ',
                '  RES  Command ran successfully',
                '',
            ]),
            $stdError,
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
                " [Drush] {$this->drushExecutable} version --format=yaml",
                "  RUN  '{$this->drushExecutable}' 'version' '--format=yaml'",
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
