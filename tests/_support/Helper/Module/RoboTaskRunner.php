<?php

namespace Helper\Module;

/**
 * Wrapper for basic shell commands and shell output.
 */
class RoboTaskRunner extends Cli
{
    /**
     * @param string $taskName
     * @param array $options
     * @param array $arguments
     *
     * @return $this
     */
    public function runRoboTask(string $taskName, array $options = [], array $arguments = [])
    {
        $cmdPattern = 'cd %s && ../../bin/robo %s';
        $cmdArgs = [
            escapeshellarg(codecept_data_dir()),
            escapeshellarg($taskName),
        ];

        foreach ($options as $option => $value) {
            $cmdPattern .= " --$option";
            if ($value !== null) {
                $cmdPattern .= '=%s';
                $cmdArgs[] = escapeshellarg($value);
            }
        }

        $cmdPattern .= str_repeat(' %s', count($arguments));
        foreach ($arguments as $argument) {
            $cmdArgs[] = escapeshellarg($argument);
        }

        $this->runShellCommand(vsprintf($cmdPattern, $cmdArgs));

        return $this;
    }
}
