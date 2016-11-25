<?php

namespace Cheppers\Robo\Drush;

class Utils
{
    public static function filterDisabled(array $items): array
    {
        return gettype(reset($items)) === 'boolean' ? array_keys($items, true, true) : $items;
    }

    public static function isValidMachineName(string $name): bool
    {
        return preg_match('/^[a-z0-9_-]+$/ui', $name);
    }
}
