<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Tests\Unit\CmdOptionHandler;

use Codeception\Test\Unit;
use Sweetchuck\Robo\Drush\CmdOptionHandler\Flag;

/**
 * @covers \Sweetchuck\Robo\Drush\CmdOptionHandler\Flag<extended>
 */
class FlagTest extends Unit
{
    /**
     * @var \Sweetchuck\Robo\Drush\Test\UnitTester
     */
    protected $tester;

    public function casesGetCommand(): array
    {
        return [
            'null' => [
                [],
                ['name' => '--foo'],
                null,
            ],
            'string - empty' => [
                [],
                ['name' => '--foo'],
                '',
            ],
            'string - 0' => [
                [],
                ['name' => '--foo'],
                '0',
            ],
            'string - bar' => [
                [],
                ['name' => '--foo'],
                'bar',
            ],
            'int - -1' => [
                [],
                ['name' => '--foo'],
                -1,
            ],
            'int - 0' => [
                [],
                ['name' => '--foo'],
                0,
            ],
            'int - 1' => [
                [],
                ['name' => '--foo'],
                1,
            ],
            'false' => [
                [],
                ['name' => '--foo'],
                false,
            ],
            'true --foo' => [
                ['--foo'],
                ['name' => '--foo'],
                true,
            ],
            'true -f' => [
                ['-f'],
                ['name' => '-f'],
                true,
            ],
            'true foo' => [
                ['--foo'],
                ['name' => 'foo'],
                true,
            ],
        ];
    }

    /**
     * @dataProvider casesGetCommand
     */
    public function testGetCommand(array $expected, array $option, $value): void
    {
        /** @var \Sweetchuck\Robo\Drush\CmdOptionHandler\Flag $handler */
        $handler = Flag::class;
        $this->tester->assertEquals($expected, $handler::getCommand($option, $value));
    }
}
