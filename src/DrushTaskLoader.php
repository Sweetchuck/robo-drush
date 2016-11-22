<?php

namespace Cheppers\Robo\Drush;

trait DrushTaskLoader
{

    /**
     * @param array $option
     *
     * @return \Cheppers\Robo\Drush\Task\CoreStatus|\Robo\Collection\CollectionBuilder
     */
    protected function taskDrushCoreStatus(array $option = [])
    {
        return $this->task(Task\CoreStatus::class, $option);
    }

    /**
     * @param array $option
     *
     * @return \Cheppers\Robo\Drush\Task\Version|\Robo\Collection\CollectionBuilder
     */
    protected function taskDrushVersion(array $option = [])
    {
        return $this->task(Task\Version::class, $option);
    }

    /**
     * @return \Cheppers\Robo\Drush\Task\PmEnable|\Robo\Collection\CollectionBuilder
     */
    protected function taskDrushPmEnable()
    {
        return $this->task(Task\PmEnable::class);
    }
}
