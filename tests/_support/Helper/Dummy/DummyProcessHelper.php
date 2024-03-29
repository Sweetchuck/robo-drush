<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Drush\Tests\Helper\Dummy;

use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyProcess;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class DummyProcessHelper extends ProcessHelper
{
    public function run(
        OutputInterface $output,
        $cmd,
        $error = null,
        callable $callback = null,
        $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE
    ): Process {
        $process = new DummyProcess($cmd);

        return parent::run($output, $process, $error, $callback, $verbosity);
    }
}
