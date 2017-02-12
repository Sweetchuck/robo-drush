<?php

namespace Cheppers\Robo\Drush\Task;

use Cheppers\AssetJar\AssetJarAware;
use Cheppers\AssetJar\AssetJarAwareInterface;
use Cheppers\Robo\Drush\CmdOptionHandlerInterface;
use Cheppers\Robo\Drush\StdOutputParser\Base as StdOutputParserBase;
use Cheppers\Robo\Drush\StdOutputParser\PmList as StdOutputParserPmList;
use Cheppers\Robo\Drush\StdOutputParser\Version as StdOutputParserVersion;
use Cheppers\Robo\Drush\CmdOptionHandler\Flag as CmdOptionHandlerFlag;
use Cheppers\Robo\Drush\CmdOptionHandler\Value as CmdOptionHandlerValue;
use Cheppers\Robo\Drush\StdOutputParserInterface;
use Cheppers\Robo\Drush\Utils;
use Robo\Common\IO;
use Robo\Contract\CommandInterface;
use Robo\Contract\OutputAwareInterface;
use Robo\Result;
use Symfony\Component\Console\Input\InputAwareInterface;
use Symfony\Component\Process\Process;

class DrushTask extends \Robo\Task\BaseTask implements
    AssetJarAwareInterface,
    CommandInterface,
    InputAwareInterface,
    OutputAwareInterface
{
    use AssetJarAware;
    use IO;

    public static $commands = [
        '' => [
            'options' => [
                'version' => [
                    'name' => 'version',
                    'handler' => CmdOptionHandlerFlag::class,
                ],
            ],
        ],
        'pm-list' => [
            'stdOutputParser' => StdOutputParserPmList::class,
        ],
    ];

    //region Options
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

    public function __construct($config, array $options = [], array $arguments = [])
    {
        if (is_string($config)) {
            $config = ['cmdName' => $config];
        }

        if (isset($config['assetJar'])) {
            $this->setAssetJar($config['assetJar']);
        }

        if (isset($config['assetJarMapping'])) {
            $this->setAssetJarMapping($config['assetJarMapping']);
        }

        if (isset($config['workingDirectory'])) {
            $this->setWorkingDirectory($config['workingDirectory']);
        }

        if (isset($config['phpExecutable'])) {
            $this->setPhpExecutable($config['phpExecutable']);
        }

        if (isset($config['drushExecutable'])) {
            $this->setDrushExecutable($config['drushExecutable']);
        }

        if (isset($config['cmdName'])) {
            $this->setCmdName($config['cmdName']);
        }

        $this
            ->setCmdOptions($options)
            ->setCmdArguments($arguments);
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

        $cmdName = $this->getCmdName();
        foreach (array_keys($this->getCmdOptions()) as $name) {
            $value = $this->getCmdOption($name);
            $option = static::$commands[$cmdName]['options'][$name] ?? ['name' => $name];

            /** @var CmdOptionHandlerInterface $optionHandler */
            $optionHandler = $option['handler'] ?? null;
            if (!$optionHandler) {
                if (gettype($value) === 'boolean') {
                    $optionHandler = CmdOptionHandlerFlag::class;
                } else {
                    $optionHandler = CmdOptionHandlerValue::class;
                }
            }

            $optionHandler::getCommand($option, $value, $cmdPattern, $cmdArgs);
        }

        if ($cmdName) {
            $cmdPattern .= ' %s';
            $cmdArgs[] = $cmdName;
        }

        $cmdArguments = Utils::filterDisabled($this->getCmdArguments());
        $cmdPattern .= str_repeat(' %s', count($cmdArguments));
        foreach ($cmdArguments as $cmdArgument) {
            $cmdArgs[] = escapeshellarg($cmdArgument);
        }

        // @todo Handle additional options.
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
            $this->output()->write($stdOutput);
            $this
                ->runParseStdOutput($stdOutput)
                ->runReleaseAssets();

            return Result::success($this, $stdOutput, $this->assets);
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
                if ($this->getCmdOption('version') === true) {
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

    /**
     * @return $this
     */
    protected function runReleaseAssets()
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
}
