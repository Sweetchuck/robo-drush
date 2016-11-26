<?php

// @codingStandardsIgnoreStart
class RoboFile extends \Robo\Tasks
{
    // @codingStandardsIgnoreEnd

    use \Cheppers\Robo\Drush\DrushTaskLoader;

    public function coreStatus(string $format = null)
    {
        return $this
            ->taskDrush('core-status')
            ->setCmdOption('format', $format);
    }

    public function version(string $format = null)
    {
        return $this
            ->taskDrush('')
            ->setCmdOption('version', true)
            ->setCmdOption('format', $format);
    }
}
