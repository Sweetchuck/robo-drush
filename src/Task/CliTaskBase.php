<?php

declare(strict_types=1);

namespace Sweetchuck\Robo\Drush\Task;

use InvalidArgumentException;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\Common\OutputAwareTrait;
use Robo\Contract\CommandInterface;
use Robo\Contract\OutputAwareInterface;
use Robo\Result;
use Robo\Task\BaseTask;
use Robo\TaskInfo;
use Sweetchuck\Robo\Drush\CmdOptionHandler\Flag as CmdOptionHandlerFlag;
use Sweetchuck\Robo\Drush\CmdOptionHandler\Value as CmdOptionHandlerValue;
use Sweetchuck\Robo\Drush\OutputParser\DefaultOutputParser;
use Sweetchuck\Robo\Drush\OutputParser\PmListOutputParser;
use Sweetchuck\Robo\Drush\OutputParser\VersionOutputParser;
use Sweetchuck\Robo\Drush\Utils;
use Sweetchuck\Utils\Filter\ArrayFilterEnabled;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Process\Process;

abstract class CliTaskBase extends BaseTask implements
    CommandInterface,
    ContainerAwareInterface,
    OutputAwareInterface
{

    use ContainerAwareTrait;
    use OutputAwareTrait;

    public static $commands = [
        // Global options.
        '' => [
            'options' => [
                'alias-path' => [
                    'settings' => [
                        'separator' => PATH_SEPARATOR,
                    ],
                ],
                'version' => [
                    'handler' => CmdOptionHandlerFlag::class,
                ],
            ],
        ],

        // Command specific default options.
        '_' => [],

        // Command specific options.
        'pm:list' => [
            'outputParser' => PmListOutputParser::class,
        ],
        'version' => [
            'outputParser' => VersionOutputParser::class,
        ],
    ];

    /**
     * @var array
     */
    protected $command = [];

    /**
     * @var array
     */
    protected $assets = [];

    /**
     * @var int
     */
    protected $processExitCode = 0;

    /**
     * @var string
     */
    protected $processStdOutput = '';

    /**
     * @var string
     */
    protected $processStdError = '';

    /**
     * @var string
     */
    protected $taskName = 'Drush';

    public function getTaskName(): string
    {
        return $this->taskName ?: TaskInfo::formatTaskName($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTaskContext($context = null)
    {
        if (!$context) {
            $context = [];
        }

        if (empty($context['name'])) {
            $context['name'] = $this->getTaskName();
        }

        return parent::getTaskContext($context);
    }

    //region Options.

    //region Option - workingDirectory
    /**
     * @var string
     */
    public $workingDirectory = '';

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
    // endregion

    //region Option - envVars.
    /**
     * @var array
     */
    protected $envVars = [];

    public function getEnvVars(): array
    {
        return $this->envVars;
    }

    /**
     * @return $this
     */
    public function setEnvVars(array $envVars)
    {
        $this->envVars = $envVars;

        return $this;
    }

    public function addEnvVars(array $envVars)
    {
        $this->envVars = $envVars + $this->envVars;

        return $this;
    }

    public function addEnvVar(string $name, string $value)
    {
        $this->envVars[$name] = $value;

        return $this;
    }

    public function removeEnvVars(array $envVars)
    {
        $this->envVars = array_diff_key($this->envVars, $envVars);

        return $this;
    }

    public function removeEnvVar(string $name)
    {
        unset($this->envVars[$name]);

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

    // region Option - cmdGlobalOptions.
    /**
     * @var array
     */
    protected $cmdGlobalOptionValues = [];

    public function getCmdGlobalOptions(): array
    {
        return $this->cmdGlobalOptionValues;
    }

    /**
     * @return $this
     */
    public function setCmdGlobalOptions(array $value)
    {
        $this->cmdGlobalOptionValues = $value;

        return $this;
    }

    public function getGlobalOption(string $name)
    {
        return $this->cmdGlobalOptionValues[$name] ?? null;
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
        if ($cmdName && !Utils::isValidCommandName($cmdName)) {
            throw new InvalidArgumentException("Invalid command name: '$cmdName'");
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
        if (!Utils::isValidCommandName($name)) {
            throw new InvalidArgumentException("Invalid option name: '$name'");
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

    //region Option - cmdExtraArguments.
    /**
     * @var array
     */
    protected $cmdExtraArguments = [];

    public function getCmdExtraArguments(): array
    {
        return $this->cmdExtraArguments;
    }

    /**
     * @return $this
     */
    public function setCmdExtraArguments(array $arguments)
    {
        $this->cmdExtraArguments = $arguments;

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
    //endregion

    //endregion

    /**
     * @return $this
     */
    public function setOptions(array $options)
    {
        if (array_key_exists('workingDirectory', $options)) {
            $this->setWorkingDirectory($options['workingDirectory']);
        }

        if (array_key_exists('envVars', $options)) {
            $this->setEnvVars($options['envVars']);
        }

        if (array_key_exists('phpExecutable', $options)) {
            $this->setPhpExecutable($options['phpExecutable']);
        }

        if (array_key_exists('drushExecutable', $options)) {
            $this->setDrushExecutable($options['drushExecutable']);
        }

        if (array_key_exists('cmdGlobalOptions', $options)) {
            $this->setCmdGlobalOptions($options['cmdGlobalOptions']);
        }

        if (array_key_exists('cmdName', $options)) {
            $this->setCmdName($options['cmdName']);
        }

        if (array_key_exists('cmdOptions', $options)) {
            $this->setCmdOptions($options['cmdOptions']);
        }

        if (array_key_exists('cmdArguments', $options)) {
            $this->setCmdArguments($options['cmdArguments']);
        }

        if (array_key_exists('cmdExtraArguments', $options)) {
            $this->setCmdExtraArguments($options['cmdExtraArguments']);
        }

        if (array_key_exists('processTimeout', $options)) {
            $this->setProcessTimeout($options['processTimeout']);
        }

        if (array_key_exists('assetNamePrefix', $options)) {
            $this->setAssetNamePrefix($options['assetNamePrefix']);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCommand()
    {
        return implode(' ', $this->getCommandArray());
    }

    protected function getCommandArray(): array
    {
        $cmdCd = [];
        $wd = $this->getWorkingDirectory();
        if ($wd) {
            $cmdCd[] = 'cd';
            $cmdCd[] = escapeshellarg($wd);
            $cmdCd[] = '&&';
        }

        $envVars = $this->getEnvVars();
        $cmdEnvVars = [];
        if (!$envVars) {
            foreach ($envVars as $name => $value) {
                if ($value !== null) {
                    $cmdEnvVars[] = sprintf("{$name}=%s", escapeshellarg($value));
                }
            }
        }

        $phpExecutable = $this->getPhpExecutable();
        $cmdExe = [];
        if ($phpExecutable) {
            $cmdExe[] = escapeshellcmd($phpExecutable);
        }
        $cmdExe[] = escapeshellcmd($this->getDrushExecutable());

        $action = $this->getCmdName();

        $cmdGlobalOptions = $this->getCommandArrayOptions($action, $this->getCmdGlobalOptions());

        $cmdAction = [];
        if ($action) {
            $cmdAction[] = $action;
        }

        $cmdOptions = $this->getCommandArrayOptions($action, $this->getCmdOptions());

        $argFilter = new ArrayFilterEnabled();
        $cmdArguments = array_filter($this->getCmdArguments(), $argFilter);
        array_walk($cmdArguments, function (&$arg) {
            $arg = escapeshellarg($arg);
        });

        $cmdExtraArguments = array_filter($this->getCmdExtraArguments(), $argFilter);
        array_walk($cmdExtraArguments, function (&$arg) {
            $arg = escapeshellarg($arg);
        });
        if ($cmdExtraArguments) {
            array_unshift($cmdExtraArguments, '--');
        }

        return array_merge(
            $cmdCd,
            $envVars,
            $cmdExe,
            $cmdGlobalOptions,
            $cmdAction,
            $cmdOptions,
            $cmdArguments,
            $cmdExtraArguments
        );
    }

    protected function getCommandArrayOptions(string $action, array $options): array
    {
        $cmd = [];
        foreach ($options as $name => $value) {
            $meta = $this->getOptionMeta($action, $name);
            $handler = $this->getOptionHandler($meta, $value);
            $cmd = array_merge($cmd, $handler::getCommand($meta, $value));
        }

        return $cmd;
    }

    protected function getOptionMeta(string $action, string $name): array
    {
        $meta = array_replace_recursive(
            static::$commands['']['options'][$name] ?? [],
            static::$commands['_']['options'][$name] ?? [],
            static::$commands[$action]['options'][$name] ?? []
        );

        $meta['name'] = $name;

        return $meta;
    }

    /**
     * @return \Sweetchuck\Robo\Drush\CmdOptionHandlerInterface
     */
    protected function getOptionHandler(array $option, $value): string
    {
        if (!empty($option['handler'])) {
            return $option['handler'];
        }

        return gettype($value) === 'boolean' ? CmdOptionHandlerFlag::class : CmdOptionHandlerValue::class;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->command = $this->getCommandArray();

        return $this
            ->runHeader()
            ->runDoIt()
            ->runPrepareAssets()
            ->runReturn();
    }

    /**
     * @return $this
     */
    protected function runHeader()
    {
        $this->printTaskInfo(implode(' ', $this->command));

        return $this;
    }

    /**
     * @return $this
     */
    public function runDoIt()
    {
        $process = $this
            ->getProcessHelper()
            ->run(
                $this->output(),
                $this->command,
                null,
                $this->getProcessRunCallbackWrapper()
            );

        $this->processExitCode = $process->getExitCode();
        $this->processStdOutput = $process->getOutput();
        $this->processStdError = $process->getErrorOutput();

        return $this;
    }

    protected function runPrepareAssets()
    {
        $action = $this->getCmdName();
        $outputParserClass = static::$commands[$action]['outputParser'] ?? DefaultOutputParser::class;
        /** @var \Sweetchuck\Robo\Drush\OutputParserInterface $outputParser */
        $outputParser = new $outputParserClass();
        $result = $outputParser->parse(
            $this,
            $this->processExitCode,
            $this->processStdOutput,
            $this->processStdError
        );

        $this->assets = $result['assets'] ?? [];

        return $this;
    }

    protected function runReturn(): Result
    {
        return new Result(
            $this,
            $this->getTaskResultCode(),
            $this->getTaskResultMessage(),
            $this->getAssetsWithPrefixedNames()
        );
    }

    protected function getTaskResultCode(): int
    {
        return $this->processExitCode;
    }

    protected function getTaskResultMessage(): string
    {
        return $this->processStdError;
    }

    protected function getProcessRunCallbackWrapper(): callable
    {
        return function (string $type, string $data): void {
            $this->processRunCallback($type, $data);
        };
    }

    protected function processRunCallback(string $type, string $data): void
    {
        switch ($type) {
            case Process::OUT:
                $this->output()->write($data);
                break;

            case Process::ERR:
                $this->printTaskError($data);
                break;
        }
    }

    protected function getAssetsWithPrefixedNames(): array
    {
        $prefix = $this->getAssetNamePrefix();
        if (!$prefix) {
            return $this->assets;
        }

        $assets = [];
        foreach ($this->assets as $key => $value) {
            $assets["{$prefix}{$key}"] = $value;
        }

        return $assets;
    }

    protected function getProcessHelper(): ProcessHelper
    {
        return $this
            ->getContainer()
            ->get('application')
            ->getHelperSet()
            ->get('process');
    }
}
