<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Tests\Unit;

use Sweetchuck\Robo\Drush\Utils;
use Codeception\Test\Unit;

/**
 * Class UtilsTest.
 *
 * @covers \Sweetchuck\Robo\Drush\Utils
 *
 * @package Sweetchuck\Robo\Drush\Tests\Unit
 */
class UtilsTest extends Unit
{
    /**
     * @var \Sweetchuck\Robo\Drush\Test\UnitTester
     */
    protected $tester;

    /**
     * @return array
     */
    public function casesFilterDisabled()
    {
        return [
            'empty' => [
                [],
                [],
            ],
            'list' => [
                ['a', 'b'],
                ['a', 'b'],
            ],
            'hash' => [
                ['a', 'c'],
                ['a' => true, 'b' => false, 'c' => true],
            ],
        ];
    }

    /**
     * @return array
     */
    public function casesIsValidCommandName()
    {
        return [
            'empty' => [false, ''],
            'space' => [false, ' '],
            'space inner' => [false, 'a a'],
            'invalid char' => [false, 'a!b'],
            'good' => [true, 'a_b-c:d'],
        ];
    }

    /**
     * @dataProvider casesIsValidCommandName
     */
    public function testIsValidCommandName(bool $expected, string $name)
    {
        $this->tester->assertEquals($expected, Utils::isValidCommandName($name));
    }
}
