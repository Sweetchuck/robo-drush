<?php

namespace Cheppers\Robo\Drush\Task;

use Cheppers\Robo\Drush\BaseTask;
use Cheppers\Robo\Drush\Component\Arguments;
use Cheppers\Robo\Drush\Component\OptionFormat;
use Cheppers\Robo\Drush\Component\OptionRoot;

class CoreStatus extends BaseTask
{
    use OptionRoot;
    use OptionFormat;
    use Arguments;

    /**
     * {@inheritdoc}
     */
    protected $drushCommand = 'core-status';

    public function __construct(array $options = [], array $items = [])
    {
        parent::__construct($options);

        if ($items) {
            $this->setArguments($items);
        }

        $this->drushOptions += [
            'root' => $this->drushOptionInfoValue('root'),
            'format' => $this->drushOptionInfoValue('format'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this
            ->setOptionsFormat($options)
            ->setOptionsRoot($options);

        return parent::setOptions($options);
    }
}
