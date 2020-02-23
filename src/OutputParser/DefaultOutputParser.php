<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\OutputParser;

use Robo\Collection\CollectionBuilder;
use Sweetchuck\Robo\Drush\OutputParserInterface;
use Symfony\Component\Yaml\Yaml;

class DefaultOutputParser implements OutputParserInterface
{
    /**
     * @var \Sweetchuck\Robo\Drush\Task\DrushTask
     */
    protected $task;

    /**
     * @var int
     */
    protected $exitCode = null;

    /**
     * @var null|string
     */
    protected $stdOutput = null;

    /**
     * @var null|string
     */
    protected $stdError = null;

    /**
     * @var array
     */
    protected $result = [];

    /**
     * @var null|string
     */
    protected $assetNameBase = null;

    /**
     * {@inheritdoc}
     */
    public function parse(
        $task,
        int $exitCode,
        string $stdOutput,
        string $stdError
    ): array {
        $this->task = $task;
        $this->exitCode = $exitCode;
        $this->stdOutput = $stdOutput;
        $this->stdError = $stdError;
        $this->result = [
            'exitCode' => $exitCode,
            'assets' => [],
        ];

        $this->parseStdOutput();

        return $this->result;
    }

    protected function parseStdOutput()
    {
        $format = $this->task->getCmdOption('format');
        $hasAsset = false;
        $asset = null;
        switch ($format) {
            case 'json':
                $hasAsset = true;
                $asset = json_decode($this->stdOutput, true);
                break;

            case 'yaml':
                $hasAsset = true;
                $asset = Yaml::parse($this->stdOutput);
                break;
        }

        if ($hasAsset) {
            if ($this->assetNameBase !== null) {
                $this->result['assets'][$this->assetNameBase] = $asset;
            } else {
                $this->result['assets'] = $asset;
            }
        }

        return $this;
    }
}
