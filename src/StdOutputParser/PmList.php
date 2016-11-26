<?php

namespace Cheppers\Robo\Drush\StdOutputParser;

use Cheppers\Robo\Drush\Task\DrushTask;

class PmList extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function parse(DrushTask $task, string $stdOutput)
    {
        $extensions = parent::parse($task, $stdOutput);
        if ($extensions) {
            $statusEnabled = $extensions['system']['status'];
            $statusMap = [
                $statusEnabled => true,
            ];

            // @todo Better detections. isset, theme_engine.
            $typeMap = [
                $extensions['system']['type'] => 'module',
                $extensions['bartik']['type'] => 'theme',
            ];

            foreach ($extensions as $id => $extension) {
                if ($extension['status'] === $statusEnabled) {
                    continue;
                }

                $statusMap[$extension['status']] = $extension['status'] === 'Not installed' ? null : false;

                if (count($statusMap) === 3) {
                    break;
                }
            }

            // @todo Support for "Not Installed" status.
            $statusMap[$extensions['system']['status']] = true;

            foreach ($extensions as $id => &$extension) {
                $extension['machine_name'] = $id;
                $extension['machine_status'] = $statusMap[$extension['status']];
                $extension['machine_type'] = $typeMap[$extension['type']];
            }
        }

        return $extensions;
    }
}
