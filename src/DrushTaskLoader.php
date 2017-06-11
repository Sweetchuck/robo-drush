<?php

namespace Cheppers\Robo\Drush;

trait DrushTaskLoader
{
    /**
     * @return \Cheppers\Robo\Drush\Task\DrushTask|\Robo\Collection\CollectionBuilder
     */
    protected function taskDrush($config, array $globalOptions = [], array $arguments = [], array $options = [])
    {
        return $this->task(Task\DrushTask::class, $config, $globalOptions, $arguments, $options);
    }
}
