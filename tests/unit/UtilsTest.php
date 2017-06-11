<?php

namespace Cheppers\Robo\Drush\Tests\Unit;

use Cheppers\Robo\Drush\Utils;
use Codeception\Test\Unit;

/**
 * Class UtilsTest.
 *
 * @covers \Cheppers\Robo\Drush\Utils
 *
 * @package Cheppers\Robo\Drush\Tests\Unit
 */
class UtilsTest extends Unit
{
    /**
     * @var \Cheppers\Robo\Drush\Test\UnitTester
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
     * @dataProvider casesFilterDisabled
     */
    public function testFilterDisabled(array $expected, array $items)
    {
        $this->tester->assertEquals($expected, Utils::filterDisabled($items));
    }

    /**
     * @return array
     */
    public function casesIsValidMachineName()
    {
        return [
            'empty' => [false, ''],
            'space' => [false, ' '],
            'space inner' => [false, 'a a'],
            'invalid char' => [false, 'a!b'],
            'good' => [true, 'a_b-c'],
        ];
    }

    /**
     * @dataProvider casesIsValidMachineName
     */
    public function testIsValidMachineName($expected, $name)
    {
        $this->tester->assertEquals($expected, Utils::isValidMachineName($name));
    }
}
