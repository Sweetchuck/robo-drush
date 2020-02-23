<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush;

use League\Container\ContainerAwareInterface;

trait DrushTaskLoader
{
    /**
     * @return \Sweetchuck\Robo\Drush\Task\DrushTask|\Robo\Collection\CollectionBuilder
     */
    protected function taskDrush(array $options = [])
    {
        /** @var \Sweetchuck\Robo\Drush\Task\DrushTask $task */
        $task = $this->task(Task\DrushTask::class);
        if ($this instanceof ContainerAwareInterface) {
            $task->setContainer($this->getContainer());
        }

        $task->setOptions($options);

        return $task;
    }
}
