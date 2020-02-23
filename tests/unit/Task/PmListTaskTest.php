<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Tests\Unit\Task;

/**
 * @covers \Sweetchuck\Robo\Drush\Task\DrushTask<extended>
 */
class PmListTaskTest extends TaskTestBase
{

    public function casesRunSuccess(): array
    {
        return [
            'drush pm-list --format=yaml' => [
                [
                    'exitCode' => 0,
                    'assets' => [
                        'extensions' => [
                            'system' => [
                                'type' => 'module',
                                'status' => 'Enabled',
                                'machine_name' => 'system',
                                'machine_status' => true,
                            ],
                            'bartik' => [
                                'type' => 'theme',
                                'status' => 'Enabled',
                                'machine_name' => 'bartik',
                                'machine_status' => true,
                            ],
                            'toolbar' => [
                                'type' => 'module',
                                'status' => 'Disabled',
                                'machine_name' => 'toolbar',
                                'machine_status' => false,
                            ],
                        ],
                    ],
                ],
                [
                    'exitCode' => 0,
                    'stdOutput' => json_encode([
                        'system' => [
                            'type' => 'module',
                            'status' => 'Enabled',
                        ],
                        'bartik' => [
                            'type' => 'theme',
                            'status' => 'Enabled',
                        ],
                        'toolbar' => [
                            'type' => 'module',
                            'status' => 'Disabled',
                        ],
                    ]),
                    'stdError' => '',
                ],
                [
                    'cmdName' => 'pm:list',
                    'cmdOptions' => [
                        'format' => 'yaml',
                    ],
                ],
            ],
        ];
    }
}
