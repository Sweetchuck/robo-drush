<?php

namespace Cheppers\Robo\Drush\Tests\Acceptance\Task;

use \AcceptanceTester;

class DrushTaskCest
{
    protected $drushVersion = '8.1.2';

    public function drushVersion(AcceptanceTester $i)
    {
        $i
            ->runRoboTask('version')
            ->expectTheExitCodeToBe(0)
            ->seeThisTextInTheStdOutput("Drush Version   :  {$this->drushVersion}");
    }

    public function drushVersionYaml(AcceptanceTester $i)
    {
        $i
            ->runRoboTask('version', [], ['yaml'])
            ->expectTheExitCodeToBe(0)
            ->seeThisTextInTheStdOutput($this->drushVersion);
    }
}
