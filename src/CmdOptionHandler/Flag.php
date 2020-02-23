<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\CmdOptionHandler;

class Flag extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function getCommand(array $option, $value): array
    {
        return $value === true ? [static::optionName($option['name'])] : [];
    }
}
