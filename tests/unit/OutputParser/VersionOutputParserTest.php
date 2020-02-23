<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Tests\Unit\OutputParser;

use Sweetchuck\Robo\Drush\OutputParser\VersionOutputParser;

/**
 * @covers \Sweetchuck\Robo\Drush\OutputParser\VersionOutputParser<extended>
 */
class VersionOutputParserTest extends TestBase
{
    /**
     * @inheritdoc
     */
    protected $parserClass = VersionOutputParser::class;

    public function casesParse(): array
    {
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
                        'drush-version' => '10.2.1',
                    ],
                ],
                [
                    'cmdOptions' => [
                        'format' => 'json',
                    ],
                ],
                0,
                implode("\n", [
                    '{',
                    '  "drush-version": "10.2.1"',
                    '}',
                    '',
                ]),
                '',
            ],
            'format=yaml' => [
                [
                    'exitCode' => 0,
                    'assets' => [
                        'drush-version' => '10.2.1',
                    ],
                ],
                [
                    'cmdOptions' => [
                        'format' => 'yaml',
                    ],
                ],
                0,
                implode("\n", [
                    'drush-version: 10.2.1',
                    ''
                ]),
                '',
            ],
            'format=var_export' => [
                [
                    'exitCode' => 0,
                    'assets' => [
                        'drush-version' => '10.2.1',
                    ],
                ],
                [
                    'cmdOptions' => [
                        'format' => 'var_export',
                    ],
                ],
                0,
                implode("\n", [
                    'array (',
                    "  'drush-version' => '10.2.1',",
                    ')',
                    '',
                ]),
                '',
            ],
        ];
    }
}
