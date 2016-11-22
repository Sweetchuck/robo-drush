<?php

namespace Cheppers\Robo\Drush;

use Cheppers\AssetJar\AssetJarAware;
use Cheppers\AssetJar\AssetJarAwareInterface;
use Robo\Common\IO;
use Robo\Contract\CommandInterface;
use Robo\Contract\OutputAwareInterface;
use Robo\Result;
use Symfony\Component\Console\Input\InputAwareInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

abstract class BaseTask extends \Robo\Task\BaseTask implements
    AssetJarAwareInterface,
    CommandInterface,
    InputAwareInterface,
    OutputAwareInterface
{
    use AssetJarAware;
    use IO;

    //region Options
    //region Option - drushExecutable
    protected $drushExecutable = '';

    public function getDrushExecutable()
    {
        return $this->drushExecutable;
    }

    public function setDrushExecutable(string $drushExecutable): self
    {
        $this->drushExecutable = $drushExecutable;

        return $this;
    }
    //endregion
    //endregion

    /**
     * @var string
     */
    protected $drushCommand = '';

    public function getDrushCommand(): string
    {
        return $this->drushCommand;
    }

    protected $drushOptions = [];

    /**
     * @var array
     */
    protected $drushArguments = [];

    /**
     * @var string
     */
    protected $processClass = Process::class;

    /**
     * @var array
     */
    protected $assets = [
        'version' => null,
    ];

    /**
     * @var string
     */
    protected $assetNameOfStdOutput = 'result';

    public function __construct(array $options = [])
    {
        $this->setDrushExecutable($this->findDrushExecutable());
        $this->setOptions($options);
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        if (isset($options['drushExecutable'])) {
            $this->setDrushExecutable($options['drushExecutable']);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand(): string
    {
        $cmdPattern = '%s';
        $cmdArgs = [$this->getDrushExecutable()];

        foreach ($this->drushOptions as $name => $option) {
            switch ($option['type']) {
                case 'flag':
                    if ($option['value']) {
                        $cmdPattern .= ' ' . $option['cli'];
                    }

                    break;

                case 'value':
                    if ($option['value'] !== null) {
                        $cmdPattern .= ' ' . $option['cli'] . '=%s';
                        $cmdArgs[] = escapeshellarg($option['value']);
                    }

                    break;

                default:
                    trigger_error("Unknown Drush command option type: '{$option['type']}'");

                    break;
            }
        }

        $drushCommand = $this->getDrushCommand();
        if ($drushCommand) {
            $cmdPattern .= ' %s';
            $cmdArgs[] = escapeshellarg($drushCommand);
        }

        $drushArguments = Utils::filterDisabled($this->drushArguments);
        $cmdPattern .= str_repeat(' %s', count($drushArguments));
        foreach ($drushArguments as $drushArgument) {
            $cmdArgs[] = escapeshellarg($drushArgument);
        }

        return vsprintf($cmdPattern, $cmdArgs);
    }

    /**
     * {@inheritdoc}
     */
    public function run(): Result
    {
        $command = $this->getCommand();
        $this->printTaskInfo(
            'Drush command: <info>{drushCommand}</info>',
            [
                'drushCommand' => $command,
            ]
        );
        /** @var \Symfony\Component\Process\Process $process */
        $process = new $this->processClass($this->getCommand());

        $exitCode = $process->run();
        if ($exitCode === 0) {
            $stdOutput = $process->getOutput();
            $this
                ->runParseStdOutput($stdOutput)
                ->runReleaseAssets();

            return Result::success($this, $stdOutput, $this->assets);
        }

        return Result::error($this, $process->getErrorOutput());
    }

    /**
     * @param string $stdOutput
     *
     * @return $this
     */
    protected function runParseStdOutput(string $stdOutput)
    {
        if (method_exists($this, 'getOptionFormat')) {
            switch ($this->getOptionFormat()) {
                case 'json':
                    $this->assets[$this->assetNameOfStdOutput] = json_decode($stdOutput);

                    break;

                case 'yaml':
                    $this->assets[$this->assetNameOfStdOutput] = Yaml::parse($stdOutput);

                    break;
            }
        }

        return $this;
    }

    protected function runReleaseAssets(): self
    {
        if ($this->hasAssetJar()) {
            foreach ($this->assets as $name => $value) {
                if ($this->getAssetJarMap($name)) {
                    $this->setAssetJarValue($name, $value);
                }
            }
        }

        return $this;
    }

    protected function findDrushExecutable(): string
    {
        $suggestions = [
            dirname($_SERVER['argv'][0]) . '/drush',
            'vendor/bin/drush',
            'bin/drush',
        ];

        foreach ($suggestions as $suggestion) {
            if (is_executable($suggestion)) {
                return $suggestion;
            }
        }

        return 'drush';
    }

    protected function drushOptionInfoValue(string $name): array
    {
        return [
            'type' => 'value',
            'cli' => "--$name",
            'value' => null,
        ];
    }

    protected function drushOptionInfoFlag(string $name, bool $value = false): array
    {
        return [
            'type' => 'flag',
            'cli' => "--$name",
            'value' => $value,
        ];
    }
}
