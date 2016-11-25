<?php

namespace Cheppers\Robo\Drush;

interface CmdOptionHandlerInterface
{
    public static function getCommand(array $option, $value, string &$cmdPattern, array &$cmdArgs);
}
