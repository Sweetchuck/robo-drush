<?php

namespace Cheppers\Robo\Drush\Task;

use Cheppers\Robo\Drush\BaseTask;
use Cheppers\Robo\Drush\Component\Arguments;
use Cheppers\Robo\Drush\Component\OptionRoot;

class PmEnable extends BaseTask
{
    use OptionRoot;
    use Arguments;

    /**
     * {@inheritdoc}
     */
    protected $drushCommand = 'pm-enable';

    public function __construct(array $options = [], array $extensions = [])
    {
        parent::__construct($options);
        $this->setArguments($extensions);

        $this->drushOptions += [
            'root' => $this->drushOptionInfoValue('root'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this->setOptionsRoot($options);

        return parent::setOptions($options);
    }
}
