<?php

namespace Sweetchuck\Robo\Drush\Tests\Acceptance\Task;

use Sweetchuck\Robo\Drush\Test\AcceptanceTester;

class DrushTaskCest
{
    protected $drushVersion = '8.1.12';

    public function drushVersion(AcceptanceTester $i): void
    {
        $id = 'version';
        $i->runRoboTask($id, \DrushRoboFile::class, 'version');
        $i->assertEquals(0, $i->getRoboTaskExitCode($id));
        $i->assertContains(
            "Drush Version   :  {$this->drushVersion}",
            $i->getRoboTaskStdOutput($id)
        );
    }

    public function drushVersionYaml(AcceptanceTester $i): void
    {
        $id = 'version yaml';
        $i->runRoboTask($id, \DrushRoboFile::class, 'version', 'yaml');
        $i->assertEquals(0, $i->getRoboTaskExitCode($id));
        $i->assertContains($this->drushVersion, $i->getRoboTaskStdOutput($id));
    }

    public function drushCoreExecute(AcceptanceTester $i): void
    {
        $id = '';
        $i->wantToTest('Process timeout');
        $i->runRoboTask($id, \DrushRoboFile::class, 'core:execute', '--process-timeout=1', 'sleep', '3');
        $i->assertEquals(1, $i->getRoboTaskExitCode($id));
        $i->assertEquals('', $i->getRoboTaskStdOutput($id));
        $i->assertContains('exceeded the timeout of 1 seconds', $i->getRoboTaskStdError($id));
    }
}
