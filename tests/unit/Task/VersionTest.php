<?php

namespace Cheppers\Robo\Drush\Tests\Unit\Task;

use Cheppers\Robo\Drush\Task\Version;
use Codeception\Test\Unit;

class VersionTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @return array
     */
    public function casesGetCommand()
    {
        return [
            'basic' => [
                'drush --version',
                [],
            ],
            'format json' => [
                "drush --format='json' --version",
                [
                    'format' => 'json',
                ],
            ],
            'my-drush' => [
                "my-drush --version",
                [
                    'drushExecutable' => 'my-drush'
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesGetCommand
     */
    public function testGetCommand(string $expected, array $options)
    {
        $task = new Version($options);
        $this->tester->assertEquals($expected, $task->getCommand());
    }
}
