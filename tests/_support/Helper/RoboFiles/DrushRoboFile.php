<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Test\Helper\RoboFiles;

use Robo\Contract\TaskInterface;
use Robo\Tasks;
use Sweetchuck\Robo\Drush\DrushTaskLoader;

class DrushRoboFile extends Tasks
{
    use DrushTaskLoader;

    protected function output()
    {
        return $this->getContainer()->get('output');
    }

    public function drush(string $taskOptions = null): TaskInterface
    {
        $taskOptions = $taskOptions === null ? [] : json_decode($taskOptions, true);

        return $this
            ->taskDrush()
            ->setOutput($this->output())
            ->setOptions($taskOptions);
    }
}
