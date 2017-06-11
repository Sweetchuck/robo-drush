<?php

namespace Cheppers\Robo\Drush\CmdOptionHandler;

use Cheppers\Robo\Drush\CmdOptionHandlerInterface;
use Cheppers\Robo\Drush\Utils;

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
