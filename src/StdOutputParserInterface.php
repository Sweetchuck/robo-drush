<?php

namespace Cheppers\Robo\Drush;

use Cheppers\Robo\Drush\Task\DrushTask;

interface StdOutputParserInterface
{
    /**
     * @return mixed
     */
    public static function parse(DrushTask $task, string $stdOutput);
}
