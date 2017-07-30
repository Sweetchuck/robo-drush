<?php

namespace Sweetchuck\Robo\Drush\Task;

use Sweetchuck\Robo\Drush\CmdOptionHandlerInterface;
use Sweetchuck\Robo\Drush\StdOutputParser\Base as StdOutputParserBase;
use Sweetchuck\Robo\Drush\StdOutputParser\PmList as StdOutputParserPmList;
use Sweetchuck\Robo\Drush\StdOutputParser\Version as StdOutputParserVersion;
use Sweetchuck\Robo\Drush\CmdOptionHandler\Flag as CmdOptionHandlerFlag;
use Sweetchuck\Robo\Drush\CmdOptionHandler\Value as CmdOptionHandlerValue;
use Sweetchuck\Robo\Drush\StdOutputParserInterface;
use Sweetchuck\Robo\Drush\Utils;
use Robo\Common\IO;
use Robo\Contract\CommandInterface;
use Robo\Contract\OutputAwareInterface;
use Robo\Result;
use Symfony\Component\Console\Input\InputAwareInterface;
use Symfony\Component\Process\Process;

class DrushTask extends \Robo\Task\BaseTask implements
    CommandInterface,
    InputAwareInterface,
    OutputAwareInterface
{
    use IO;

    public static $globalOptions = [
        'alias-path' => [
            'settings' => [
                'separator' => PATH_SEPARATOR,
            ],
        ],
    ];

    public static $commands = [
        '' => [
            'options' => [
                'version' => [
                    'handler' => CmdOptionHandlerFlag::class,
                ],
            ],
        ],
        'pm-list' => [
            'stdOutputParser' => StdOutputParserPmList::class,
        ],
    ];

    //region Options

    // region Option - assetNamePrefix.
    /**
     * @var string
     */
    protected $assetNamePrefix = '';

    public function getAssetNamePrefix(): string
    {
        return $this->assetNamePrefix;
    }

    /**
     * @return $this
     */
    public function setAssetNamePrefix(string $value)
    {
        $this->assetNamePrefix = $value;

        return $this;
    }
    // endregion

    //region Option - workingDirectory.
    /**
     * @var string
     */
    protected $workingDirectory = '';

    public function getWorkingDirectory(): string
    {
        return $this->workingDirectory;
    }

    /**
     * @return $this
     */
    public function setWorkingDirectory(string $workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;

        return $this;
    }
    //endregion

    //region Option - phpExecutable.
    /**
     * @var string
     */
    protected $phpExecutable = '';

    public function getPhpExecutable(): string
    {
        return $this->phpExecutable;
    }

    /**
     * @return $this
     */
    public function setPhpExecutable(string $phpExecutable)
    {
        $this->phpExecutable = $phpExecutable;

        return $this;
    }
    //endregion

    //region Option - drushExecutable
    protected $drushExecutable = '';

    public function getDrushExecutable()
    {
        return $this->drushExecutable;
    }

    /**
     * @return $this
     */
    public function setDrushExecutable(string $drushExecutable)
    {
        $this->drushExecutable = $drushExecutable;

        return $this;
    }
    //endregion

    // region Option - processTimeout.
    /**
     * @var int|null
     */
    protected $processTimeout = null;

    public function getProcessTimeout(): ?int
    {
        return $this->processTimeout;
    }

    /**
     * @return $this
     */
    public function setProcessTimeout(?int $value)
    {
        $this->processTimeout = $value;

        return $this;
    }
    // endregion

    // region Option - globalOptions.
    /**
     * @var array
     */
    protected $globalOptionValues = [];

    public function getGlobalOptions(): array
    {
        return $this->globalOptionValues;
    }

    /**
     * @return $this
     */
    public function setGlobalOptions(array $value)
    {
        $this->globalOptionValues = $value;

        return $this;
    }

    public function getGlobalOption(string $name)
    {
        return $this->globalOptionValues[$name] ?? null;
    }
    // endregion

    //region Option - cmdName.
    /**
     * @var string
     */
    protected $cmdName = '';

    public function getCmdName(): string
    {
        return $this->cmdName;
    }

    /**
     * @return $this
     */
    public function setCmdName(string $cmdName)
    {
        if ($cmdName && !Utils::isValidMachineName($cmdName)) {
            throw new \InvalidArgumentException("Invalid command name: '$cmdName'");
        }

        $this->cmdName = $cmdName;

        return $this;
    }
    //endregion

    //region Option - cmdOptions.
    protected $cmdOptions = [];

    public function getCmdOptions(): array
    {
        return $this->cmdOptions;
    }

    /**
     * @return $this
     */
    public function setCmdOptions(array $options)
    {

        foreach ($options as $name => $value) {
            $this->setcmdOption($name, $value);
        }

        return $this;
    }

    public function getCmdOption(string $name)
    {
        return $this->cmdOptions[$name] ?? null;
    }

    /**
     * @return $this
     */
    public function setCmdOption(string $name, $value)
    {
        if (!Utils::isValidMachineName($name)) {
            throw new \InvalidArgumentException("Invalid option name: '$name'");
        }

        $this->cmdOptions[$name] = $value;

        return $this;
    }
    //endregion

    //region Option - cmdArguments.
    /**
     * @var array
     */
    protected $cmdArguments = [];

    public function getCmdArguments(): array
    {
        return $this->cmdArguments;
    }

    /**
     * @return $this
     */
    public function setCmdArguments(array $cmdArguments)
    {
        $this->cmdArguments = $cmdArguments;

        return $this;
    }
    //endregion
    //endregion

    /**
     * @var string
     */
    protected $processClass = Process::class;

    /**
     * @var array
     */
    protected $assets = [];

    /**
     * @var string
     */
    protected $assetNameOfStdOutput = 'result';

    public function __construct($config, array $globalOptions = [], array $arguments = [], array $options = [])
    {
        if (is_string($config)) {
            $config = ['cmdName' => $config];
        }

        $this
            ->setConfiguration($config)
            ->setGlobalOptions($globalOptions)
            ->setCmdArguments($arguments)
            ->setCmdOptions($options);
    }

    /**
     * @return $this
     */
    public function setConfiguration(array $config)
    {
        foreach ($config as $key => $value) {
            switch ($key) {
                case 'assetNamePrefix':
                    $this->setAssetNamePrefix($value);
                    break;

                case 'workingDirectory':
                    $this->setWorkingDirectory($value);
                    break;

                case 'phpExecutable':
                    $this->setPhpExecutable($value);
                    break;

                case 'drushExecutable':
                    $this->setDrushExecutable($value);
                    break;

                case 'cmdName':
                    $this->setCmdName($value);
                    break;
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand(): string
    {
        $cmdPattern = '';
        $cmdArgs = [];

        $workingDirectory = $this->getWorkingDirectory();
        if ($workingDirectory) {
            $cmdPattern .= 'cd %s && ';
            $cmdArgs[] = escapeshellarg($workingDirectory);
        }

        $phpExecutable = $this->getPhpExecutable();
        if ($phpExecutable) {
            $cmdPattern .= '%s ';
            $cmdArgs[] = escapeshellcmd($phpExecutable);
        }

        $drushExecutable = $this->getDrushExecutable();
        if (!$drushExecutable) {
            $drushExecutable = $this->findDrushExecutable();
        }

        $cmdPattern .= '%s';
        $cmdArgs[] = $phpExecutable ?
            escapeshellarg($drushExecutable)
            : escapeshellcmd($drushExecutable);

        foreach ($this->getGlobalOptions() as $optionName => $optionValue) {
            $cliOptionName = strlen($optionName) === 1 ? "-$optionName" : "--$optionName";
            $option = static::$globalOptions[$optionName] ?? [];
            $option += ['name' => $cliOptionName];
            /** @var CmdOptionHandlerInterface $optionHandler */
            $optionHandler = $this->getOptionHandler($option, $optionValue);
            $optionHandler::getCommand($option, $optionValue, $cmdPattern, $cmdArgs);
        }

        $cmdName = $this->getCmdName();
        if ($cmdName) {
            $cmdPattern .= ' %s';
            $cmdArgs[] = $cmdName;
        }

        $cmdArguments = Utils::filterDisabled($this->getCmdArguments());
        $cmdPattern .= str_repeat(' %s', count($cmdArguments));
        foreach ($cmdArguments as $cmdArgument) {
            $cmdArgs[] = escapeshellarg($cmdArgument);
        }

        foreach ($this->getCmdOptions() as $optionName => $optionValue) {
            $cliOptionName = strlen($optionName) === 1 ? "-$optionName" : "--$optionName";
            $option = static::$commands[$cmdName]['options'][$optionName] ?? [];
            $option += ['name' => $cliOptionName];

            /** @var CmdOptionHandlerInterface $optionHandler */
            $optionHandler = $this->getOptionHandler($option, $optionValue);
            $optionHandler::getCommand($option, $optionValue, $cmdPattern, $cmdArgs);
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
        $process = new $this->processClass(
            $command,
            null,
            null,
            null,
            $this->getProcessTimeout()
        );

        $exitCode = $process->run();
        if ($exitCode === 0) {
            $stdOutput = $process->getOutput();
            $this->output()->write($stdOutput);
            $this->runParseStdOutput($stdOutput);

            return Result::success($this, $stdOutput, $this->getAssetsWithPrefixedNames());
        }

        return Result::error($this, $process->getErrorOutput());
    }

    /**
     * @return $this
     */
    protected function runParseStdOutput(string $stdOutput)
    {
        $cmdName = $this->getCmdName();
        $command = static::$commands[$cmdName] ?? [];

        /** @var StdOutputParserInterface $stdOutputParser */
        $stdOutputParser = $command['stdOutputParser'] ?? null;
        if ($stdOutputParser === null) {
            if (!$cmdName) {
                if ($this->getGlobalOption('version') === true) {
                    $stdOutputParser = StdOutputParserVersion::class;
                }
            } else {
                $stdOutputParser = StdOutputParserBase::class;
            }
        }

        if ($stdOutputParser) {
            $this->assets[$this->assetNameOfStdOutput] = $stdOutputParser::parse($this, $stdOutput);
        }

        return $this;
    }

    protected function getAssetsWithPrefixedNames(): array
    {
        $prefix = $this->getAssetNamePrefix();
        if (!$prefix) {
            return $this->assets;
        }

        $data = [];
        foreach ($this->assets as $key => $value) {
            $data["{$prefix}{$key}"] = $value;
        }

        return $data;
    }

    /**
     * @todo Improve.
     */
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

    protected function getOptionHandler(array $option, $value): string
    {
        if (!empty($option['handler'])) {
            return $option['handler'];
        }

        return gettype($value) === 'boolean' ? CmdOptionHandlerFlag::class : CmdOptionHandlerValue::class;
    }
}
