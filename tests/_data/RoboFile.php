<?php

// @codingStandardsIgnoreStart
class RoboFile extends \Robo\Tasks
{
    // @codingStandardsIgnoreEnd

    use \Cheppers\Robo\Drush\DrushTaskLoader;

    public function coreStatus(string $format = null)
    {
        return $this
            ->taskDrushCoreStatus()
            ->setOptionFormat($format)
            ->setOutput($this->output());
    }

    public function version(string $format = null)
    {
        return $this
            ->taskDrushVersion()
            ->setOptionFormat($format)
            ->setOutput($this->output());
    }

    public function pmEnable()
    {
        return $this
            ->taskDrushPmEnable()
            ->setArguments(['devel', 'simpletest']);
    }
}
