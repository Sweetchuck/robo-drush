<?php

namespace Cheppers\Robo\Drush;

trait DrushTaskLoader
{

    /**
     * @return \Cheppers\Robo\Drush\Task\DrushTask|\Robo\Collection\CollectionBuilder
     */
    protected function taskDrush($config, array $options = [], array $arguments = [])
    {
        return $this->task(Task\DrushTask::class, $config, $options, $arguments);
    }
}
