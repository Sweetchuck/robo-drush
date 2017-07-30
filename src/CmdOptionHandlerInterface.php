<?php

namespace Sweetchuck\Robo\Drush;

interface CmdOptionHandlerInterface
{
    /**
     * Add the $option and $value to the $cmdPattern and $cmdArgs.
     */
    public static function getCommand(array $option, $value, string &$cmdPattern, array &$cmdArgs): void;
}
