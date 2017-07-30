<?php

namespace Sweetchuck\Robo\Drush\CmdOptionHandler;

use Sweetchuck\Robo\Drush\CmdOptionHandlerInterface;
use Sweetchuck\Robo\Drush\Utils;

class Value implements CmdOptionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getCommand(array $option, $value, string &$cmdPattern, array &$cmdArgs): void
    {
        $defaultOption = [
            'settings' => [
                'separator' => ',',
            ],
        ];
        $option = array_replace_recursive($defaultOption, $option);

        $items = null;

        if ($value || $value === '') {
            $items = is_array($value) ? Utils::filterDisabled($value) : [$value];
        }

        if ($items) {
            $cliValue = implode($option['settings']['separator'], $items);
            $cmdPattern .= " {$option['name']}=%s";
            $cmdArgs[] = escapeshellarg($cliValue);
        }
    }
}
