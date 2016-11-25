<?php

namespace Cheppers\Robo\Drush\Tests\Unit\Task;

use Cheppers\Robo\Drush\Task\DrushTask;
use Codeception\Test\Unit;

class DrushTaskTest extends Unit
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
        $task = new DrushTask($config, $options, $arguments);
        $this->tester->assertEquals($expected, $task->getCommand());
    }
}
