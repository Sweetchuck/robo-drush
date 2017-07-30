<?php

namespace Sweetchuck\Robo\Drush;

use Sweetchuck\Robo\Drush\Task\DrushTask;

interface StdOutputParserInterface
{
    /**
     * @return mixed
     */
    public static function parse(DrushTask $task, string $stdOutput);
}
