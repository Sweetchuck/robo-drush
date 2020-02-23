<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Tests\Unit\CmdOptionHandler;

use Codeception\Test\Unit;
use Sweetchuck\Robo\Drush\CmdOptionHandler\TriState;

/**
 * @covers \Sweetchuck\Robo\Drush\CmdOptionHandler\TriState<extended>
 */
class TriStateTest extends Unit
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
                ['--foo'],
                ['name' => '--foo'],
                true,
            ],
            'false' => [
                ['--no-foo'],
                ['name' => '--foo'],
                false,
            ],
        ];
    }

    /**
     * @dataProvider casesGetCommand
     */
    public function testGetCommand(array $expected, array $option, $value): void
    {
        /** @var \Sweetchuck\Robo\Drush\CmdOptionHandler\TriState $handler */
        $handler = TriState::class;
        $this->tester->assertEquals($expected, $handler::getCommand($option, $value));
    }
}
