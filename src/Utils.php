<?php

namespace Cheppers\Robo\Drush;

class Utils
{
    public static function filterDisabled(array $items): array
    {
        return gettype(reset($items)) === 'boolean' ? array_keys($items, true, true) : $items;
    }
}
