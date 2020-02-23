<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\CmdOptionHandler;

class TriState extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function getCommand(array $option, $value): array
    {
        if ($value === null) {
            return [];
        }

        $name = static::optionName($option['name']);

        return $value ? [$name] : [preg_replace('/^--/', '--no-', $name)];
    }
}
