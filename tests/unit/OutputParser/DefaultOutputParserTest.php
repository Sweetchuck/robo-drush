<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Tests\Unit\OutputParser;

use Sweetchuck\Robo\Drush\OutputParser\DefaultOutputParser;

/**
 * @covers \Sweetchuck\Robo\Drush\OutputParser\DefaultOutputParser<extended>
 */
class DefaultOutputParserTest extends TestBase
{
    /**
     * @inheritdoc
     */
    protected $parserClass = DefaultOutputParser::class;

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
            'format=yaml' => [
                [
                    'exitCode' => 0,
                    'assets' => [
                        'a' => ['b' => 'c'],
                        'd' => ['e' => 'f'],
                    ],
                ],
                [
                    'cmdOptions' => [
                        'format' => 'yaml',
                    ],
                ],
                0,
                implode("\n", [
                    'a:',
                    '  b: c',
                    'd:',
                    '  e: f',
                    '',
                ]),
                '',
            ],
            'format=json' => [
                [
                    'exitCode' => 0,
                    'assets' => [
                        'a' => ['b' => 'c'],
                        'd' => ['e' => 'f'],
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
                    '  "a": {',
                    '    "b": "c"',
                    '  },',
                    '  "d": {',
                    '    "e": "f"',
                    '  }',
                    '}',
                    '',
                ]),
                '',
            ],
        ];
    }
}
