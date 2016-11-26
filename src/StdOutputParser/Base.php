<?php

namespace Cheppers\Robo\Drush\StdOutputParser;

use Cheppers\Robo\Drush\StdOutputParserInterface;
use Cheppers\Robo\Drush\Task\DrushTask;
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
