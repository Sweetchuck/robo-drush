<?php

namespace Cheppers\Robo\Drush\Tests\Acceptance\Task;

use Cheppers\Robo\Drush\Test\AcceptanceTester;

class DrushTaskCest
{
    protected $drushVersion = '8.1.12';

    public function drushVersion(AcceptanceTester $i)
    {
        $id = 'version';
        $i->runRoboTask($id, \DrushRoboFile::class, 'version');
        $i->assertEquals(0, $i->getRoboTaskExitCode($id));
        $i->assertContains(
            "Drush Version   :  {$this->drushVersion}",
            $i->getRoboTaskStdOutput($id)
        );
    }

    public function drushVersionYaml(AcceptanceTester $i)
    {
        $id = 'version yaml';
        $i->runRoboTask($id, \DrushRoboFile::class, 'version', 'yaml');
        $i->assertEquals(0, $i->getRoboTaskExitCode($id));
        $i->assertContains($this->drushVersion, $i->getRoboTaskStdOutput($id));
    }
}
