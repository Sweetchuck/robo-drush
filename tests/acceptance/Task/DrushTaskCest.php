<?php

namespace Sweetchuck\Robo\Drush\Tests\Acceptance\Task;

use Sweetchuck\Robo\Drush\Test\AcceptanceTester;
use Symfony\Component\Yaml\Yaml;

class DrushTaskCest
{
    /**
     * @var string
     */
    protected $drushVersion = '9.2.1';

    public function drushVersion(AcceptanceTester $tester): void
    {
        $id = 'version';
        $tester->runRoboTask($id, \DrushRoboFile::class, 'version');

        $exitCode = $tester->getRoboTaskExitCode($id);
        $stdOutput = $tester->getRoboTaskStdOutput($id);
        $stdError = $tester->getRoboTaskStdError($id);

        $tester->assertEquals(0, $exitCode);
        $tester->assertEquals(
            implode(PHP_EOL, [
                " Drush version : {$this->drushVersion} ",
                '',
            ]),
            $stdOutput
        );
        $tester->assertEquals(
            implode(PHP_EOL, [
                " [Sweetchuck\Robo\Drush\Task\DrushTask] Drush command: bin/drush version",
                '',
            ]),
            $stdError
        );
    }

    public function drushVersionYaml(AcceptanceTester $tester): void
    {
        $id = 'version:yaml';
        $tester->runRoboTask($id, \DrushRoboFile::class, 'version', 'yaml');

        $exitCode = $tester->getRoboTaskExitCode($id);
        $stdOutput = $tester->getRoboTaskStdOutput($id);
        $stdError = $tester->getRoboTaskStdError($id);

        $tester->assertEquals(0, $exitCode);
        $tester->assertEquals(
            implode(PHP_EOL, [
                " [Sweetchuck\Robo\Drush\Task\DrushTask] Drush command: bin/drush version --format='yaml'",
                '',
            ]),
            $stdError
        );

        $tester->assertEquals(
            [
                'drush-version' => $this->drushVersion
            ],
            Yaml::parse($stdOutput)
        );
    }

    public function drushCoreExecute(AcceptanceTester $i): void
    {
        $id = 'core:execute:process-timeout';
        $i->wantToTest('Process timeout');
        $i->runRoboTask($id, \DrushRoboFile::class, 'core:execute', '--process-timeout=1', 'sleep', '3');
        $i->assertEquals(1, $i->getRoboTaskExitCode($id));
        $i->assertEquals('', $i->getRoboTaskStdOutput($id));
        $i->assertContains('exceeded the timeout of 1 seconds', $i->getRoboTaskStdError($id));
    }
}
