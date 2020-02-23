<?php

declare(strict_types=1);

namespace Sweetchuck\Robo\Drush;

use Sweetchuck\Robo\Drush\Task\CliTaskBase;

interface OutputParserInterface
{
    public function parse(
        CliTaskBase $task,
        int $exitCode,
        string $stdOutput,
        string $stdError
    ): array;
}
