<?php

// @codingStandardsIgnoreStart
class DrushRoboFile extends \Robo\Tasks
{
    // @codingStandardsIgnoreEnd

    use \Cheppers\Robo\Drush\DrushTaskLoader;

    public function version(string $format = null)
    {
        return $this
            ->taskDrush('')
            ->setOutput($this->output())
            ->setCmdOption('version', true)
            ->setCmdOption('format', $format);
    }
}
