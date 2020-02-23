<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\OutputParser;

class PmList extends Base
{
    /**
     * @inheritdoc
     */
    protected $assetNameBase = 'extensions';

    protected function parseStdOutput()
    {
        parent::parseStdOutput();
        if (empty($this->result['assets'])) {
            return $this;
        }

        // @todo That is not sure the "system" and the "bartik" extensions are in the list.
        $statusEnabled = $this->result['assets'][$this->assetNameBase]['system']['status'];
        foreach ($this->result['assets'][$this->assetNameBase] as $id => &$extension) {
            $extension['machine_name'] = $id;
            $extension['machine_status'] = $statusEnabled === $extension['status'];
        }

        return $this;
    }
}
