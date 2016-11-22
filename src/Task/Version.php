<?php

namespace Cheppers\Robo\Drush\Task;

use Cheppers\Robo\Drush\BaseTask;
use Cheppers\Robo\Drush\Component\OptionFormat;

class Version extends BaseTask
{
    use OptionFormat;

    public function __construct(array $options = [])
    {
        $this->drushOptions += [
            'format' => $this->drushOptionInfoValue('format'),
            'version' => $this->drushOptionInfoFlag('version', true),
        ];

        parent::__construct($options);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this->setOptionsFormat($options);

        return parent::setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    protected function runParseStdOutput(string $stdOutput)
    {
        $version = null;
        switch ($this->getOptionFormat()) {
            case null:
            case '':
            case 'key-value':
                $parts = explode(':', $stdOutput, 2);
                if (isset($parts[1])) {
                    $version = trim($parts[1]);
                }

                break;

            case 'var_export':
                $parts = explode('=', $stdOutput, 2);
                if (isset($parts[1])) {
                    $version = trim($parts[1], "'; \t\n");
                }

                break;

            case 'json':
                $version = json_decode($stdOutput);

                break;

            case 'string':
            case 'yaml':
                $version = trim($stdOutput);

                break;
        }

        $this->assets['version'] = $version;

        return $this;
    }
}
