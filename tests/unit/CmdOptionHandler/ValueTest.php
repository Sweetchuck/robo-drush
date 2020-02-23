<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Tests\Unit\CmdOptionHandler;

use Codeception\Test\Unit;
use Sweetchuck\Robo\Drush\CmdOptionHandler\Value;

/**
 * @covers \Sweetchuck\Robo\Drush\CmdOptionHandler\Value<extended>
 */
class ValueTest extends Unit
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
            'true' => [
                ['--foo=1'],
                ['name' => '--foo'],
                true,
            ],
            'false' => [
                [],
                ['name' => '--foo'],
                false,
            ],
            'string - empty' => [
                ['--foo='],
                ['name' => '--foo'],
                '',
            ],
            'string - 0' => [
                ["--foo=0"],
                ['name' => '--foo'],
                '0',
            ],
            'array - empty' => [
                [],
                ['name' => '--foo'],
                [],
            ],
            'array - null' => [
                ['--foo='],
                ['name' => '--foo'],
                [null],
            ],
            'array - true' => [
                ['--foo=1'],
                ['name' => '--foo'],
                [true],
            ],
            'array - false' => [
                [],
                ['name' => '--foo'],
                [false],
            ],
            'array - a, b, c' => [
                ['--foo=a,,b,c'],
                ['name' => '--foo'],
                ['a', null, 'b', false, 'c'],
            ],
        ];
    }

    /**
     * @dataProvider casesGetCommand
     */
    public function testGetCommand(array $expected, array $option, $value): void
    {
        /** @var \Sweetchuck\Robo\Drush\CmdOptionHandler\Value $handler */
        $handler = Value::class;
        $this->tester->assertEquals($expected, $handler::getCommand($option, $value));
    }
}
