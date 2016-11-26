<?php

namespace Cheppers\Robo\Drush;

use Cheppers\Robo\Drush\Task\DrushTask;

interface StdOutputParserInterface
{
    /**
     * @param \Cheppers\Robo\Drush\Task\DrushTask $task
     * @param string $stdOutput
     *
     * @return mixed
     */
    public static function parse(DrushTask $task, string $stdOutput);
}
