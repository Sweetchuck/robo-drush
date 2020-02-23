<?php

declare(strict_types=1);

namespace Sweetchuck\Robo\Drush\CmdOptionHandler;

use Sweetchuck\Robo\Drush\CmdOptionHandlerInterface;

abstract class Base implements CmdOptionHandlerInterface
{

    protected static function optionName(string $name): string
    {
        if (mb_substr($name, 0, 1) === '-') {
            return $name;
        }

        return "--$name";
    }
}
