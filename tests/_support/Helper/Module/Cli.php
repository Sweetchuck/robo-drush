<?php

namespace Helper\Module;

use Codeception\Module as CodeceptionModule;
use Symfony\Component\Process\Process;

/**
 * Wrapper for basic shell commands and shell output.
 */
class Cli extends CodeceptionModule
{
    /**
     * @var \Symfony\Component\Process\Process
     */
    protected $process;

    /**
     * @var int|null
     */
    protected $exitCode = null;

    /**
     * @var string
     */
    protected $stdOutput = null;

    /**
     * @var string
     */
    protected $stdError = null;

    // @codingStandardsIgnoreStart
    public function _cleanup()
    {
        // @codingStandardsIgnoreEnd
        $this->process = null;
        $this->exitCode = null;
        $this->stdOutput = null;
        $this->stdError = null;
    }

    /**
     * Executes a shell command.
     *
     * @param string $command
     *
     * @return $this
     */
    public function runShellCommand(string $command)
    {
        $this->process = new Process($command);
        $this->exitCode = $this->process->run();
        $this->stdOutput = $this->process->getOutput();
        $this->stdError = $this->process->getErrorOutput();

        return $this;
    }

    /**
     * @param string $expected
     *
     * @return $this
     */
    public function seeThisTextInTheStdOutput(string $expected)
    {
        $this->assertContains($expected, $this->getStdOutput());

        return $this;
    }

    /**
     * @param string $expected
     *
     * @return $this
     */
    public function seeThisTextInTheStdError(string $expected)
    {
        $this->assertContains($expected, $this->getStdError());

        return $this;
    }

    /**
     * @param int $expected
     *
     * @return $this
     */
    public function expectTheExitCodeToBe(int $expected)
    {
        $this->assertEquals($expected, $this->getExitCode());

        return $this;
    }

    /**
     * @return int|null
     */
    protected function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @return string
     */
    protected function getStdOutput()
    {
        return $this->stdOutput;
    }

    /**
     * @return string
     */
    protected function getStdError()
    {
        return $this->stdError;
    }
}
