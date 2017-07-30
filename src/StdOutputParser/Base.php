<?php

namespace Sweetchuck\Robo\Drush\StdOutputParser;

use Sweetchuck\Robo\Drush\StdOutputParserInterface;
use Sweetchuck\Robo\Drush\Task\DrushTask;
use Symfony\Component\Yaml\Yaml;

class Base implements StdoutputParserInterface
{
    /**
     * {@inheritdoc}
     */
    public static function parse(DrushTask $task, string $stdOutput)
    {
        $format = $task->getCmdOption('format');
        switch ($format) {
            case 'json':
                return json_decode($stdOutput, true);

            case 'yaml':
                return Yaml::parse($stdOutput);
        }

        return null;
    }
}
