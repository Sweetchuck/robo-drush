<?php

declare(strict_types=1);

namespace Sweetchuck\Robo\Drush;

use Robo\Collection\CollectionBuilder;
use Sweetchuck\Robo\Drush\Task\CliTaskBase;

interface OutputParserInterface
{
    /**
     * @param \Sweetchuck\Robo\Drush\Task\CliTaskBase|\Robo\Collection\CollectionBuilder $task
     * @param int $exitCode
     * @param string $stdOutput
     * @param string $stdError
     *
     * @return array
     */
    public function parse(
        $task,
        int $exitCode,
        string $stdOutput,
        string $stdError
    ): array;
}
