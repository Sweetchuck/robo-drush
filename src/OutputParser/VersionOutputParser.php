<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\OutputParser;

use Symfony\Component\Yaml\Yaml;

class VersionOutputParser extends DefaultOutputParser
{
    /**
     * @inheritdoc
     */
    protected $assetNameBase = 'drush-version';

    protected function parseStdOutput()
    {
        $format = $this->task->getCmdOption('format');

        $hasAsset = false;
        $asset = null;
        switch ($format) {
            case 'json':
                $hasAsset = true;
                $asset = json_decode($this->stdOutput, true);
                $asset = $asset['drush-version'] ?? null;
                break;

            case 'yaml':
                $hasAsset = true;
                $asset = Yaml::parse($this->stdOutput);
                $asset = $asset['drush-version'] ?? null;
                break;

            case 'var_export':
                $hasAsset = true;
                $parts = explode('=>', $this->stdOutput, 2);
                if (isset($parts[1])) {
                    $hasAsset = true;
                    $asset = trim($parts[1], "', \t\n)");
                }
                break;

            case '':
            case 'string':
                $hasAsset = true;
                $asset = trim($this->stdOutput);
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
