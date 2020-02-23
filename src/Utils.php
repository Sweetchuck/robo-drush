<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush;

class Utils
{
    public static function isValidCommandName(string $name): bool
    {
        return (bool) preg_match('/^[a-z0-9:_-]+$/ui', $name);
    }
}
