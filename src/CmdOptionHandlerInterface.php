<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush;

interface CmdOptionHandlerInterface
{
    /**
     * Add the $option and $value to the $cmdPattern and $cmdArgs.
     */
    public static function getCommand(array $option, $value): array;
}
