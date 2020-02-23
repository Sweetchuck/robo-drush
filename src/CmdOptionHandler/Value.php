<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\CmdOptionHandler;

use Sweetchuck\Utils\Filter\ArrayFilterEnabled;

class Value extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function getCommand(array $option, $value): array
    {
        $defaultOption = [
            'settings' => [
                'separator' => ',',
            ],
        ];
        $option = array_replace_recursive($defaultOption, $option);

        $items = [];
        if (is_array($value)) {
            $items = array_filter($value, new ArrayFilterEnabled());
        } elseif ($value !== false && $value !== null) {
            $items = [$value];
        }

        $cmd = [];
        if ($items) {
            $cmd[] = sprintf(
                '%s=%s',
                static::optionName($option['name']),
                implode($option['settings']['separator'], $items)
            );
        }

        return $cmd;
    }
}
