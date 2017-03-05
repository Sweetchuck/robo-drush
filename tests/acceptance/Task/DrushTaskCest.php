<?php

namespace Cheppers\Robo\Drush\Tests\Acceptance\Task;

use \AcceptanceTester;

class DrushTaskCest
{
    protected $drushVersion = '8.1.10';

    public function drushVersion(AcceptanceTester $i)
    {
        $i->runRoboTask('version');
        $i->expectTheExitCodeToBe(0);
        $i->seeThisTextInTheStdOutput("Drush Version   :  {$this->drushVersion}");
    }

    public function drushVersionYaml(AcceptanceTester $i)
    {
        $i->runRoboTask('version', [], ['yaml']);
        $i->expectTheExitCodeToBe(0);
        $i->seeThisTextInTheStdOutput($this->drushVersion);
    }
}
