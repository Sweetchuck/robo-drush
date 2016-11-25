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
            ->setCmdOption('format', $format)
            ->setOutput($this->output());
    }

    public function version(string $format = null)
    {
        return $this
            ->taskDrush('')
            ->setCmdOption('version', true)
            ->setCmdOption('format', $format)
            ->setOutput($this->output());
    }

    public function pmEnable()
    {
        return $this
            ->taskDrush('pm-enable')
            ->setCmdArguments(['devel', 'simpletest']);
    }
}
