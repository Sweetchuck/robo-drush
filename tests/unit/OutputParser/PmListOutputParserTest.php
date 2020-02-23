<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Tests\Unit\OutputParser;

use Sweetchuck\Robo\Drush\OutputParser\PmListOutputParser;
use Symfony\Component\Yaml\Yaml;

/**
 * @covers \Sweetchuck\Robo\Drush\OutputParser\PmListOutputParser<extended>
 */
class PmListOutputParserTest extends TestBase
{
    /**
     * @inheritdoc
     */
    protected $parserClass = PmListOutputParser::class;

    public function casesParse(): array
    {
        $stdOutput = [
            'system' => [
                'package' => 'Core',
                'display_name' => 'System',
                'status' => 'Enabled',
                'version' => '8.8.2',
            ],
            'node' => [
                'package' => 'Core',
                'display_name' => 'Node',
                'status' => 'Enabled',
                'version' => '8.8.2',
            ],
            'color' => [
                'package' => 'Core',
                'display_name' => 'Color',
                'status' => 'Disabled',
                'version' => '8.8.2',
            ],
        ];
        $expected = $stdOutput;
        $expected['system']['machine_name'] = 'system';
        $expected['system']['machine_status'] = true;
        $expected['node']['machine_name'] = 'node';
        $expected['node']['machine_status'] = true;
        $expected['color']['machine_name'] = 'color';
        $expected['color']['machine_status'] = false;

        return [
            'without format' => [
                [
                    'exitCode' => 0,
                    'assets' => [],
                ],
                [],
                0,
                '',
                '',
            ],
            'format=json' => [
                [
                    'exitCode' => 0,
                    'assets' => [
                        'extensions' => $expected,
                    ],
                ],
                [
                    'cmdOptions' => [
                        'format' => 'json',
                    ],
                ],
                0,
                json_encode($stdOutput),
                '',
            ],
            'format=yaml' => [
                [
                    'exitCode' => 0,
                    'assets' => [
                        'extensions' => $expected,
                    ],
                ],
                [
                    'cmdOptions' => [
                        'format' => 'yaml',
                    ],
                ],
                0,
                Yaml::dump($stdOutput),
                '',
            ],
        ];
    }
}
