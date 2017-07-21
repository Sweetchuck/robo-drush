<?php

use Robo\Contract\TaskInterface;

// @codingStandardsIgnoreStart
class DrushRoboFile extends \Robo\Tasks
{
    // @codingStandardsIgnoreEnd

    use \Cheppers\Robo\Drush\DrushTaskLoader;

    public function version(string $format = null): TaskInterface
    {
        return $this
            ->taskDrush('')
            ->setOutput($this->output())
            ->setCmdOption('version', true)
            ->setCmdOption('format', $format);
    }

    public function coreExecute(
        array $cmdArguments,
        array $options = [
            'process-timeout' => 0
        ]
    ): TaskInterface {
        settype($options['process-timeout'], 'integer');

        return $this
            ->taskDrush('core-execute')
            ->setOutput($this->output())
            ->setProcessTimeout($options['process-timeout'] ?? null)
            ->setCmdArguments($cmdArguments);
    }
}
