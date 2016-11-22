<?php

namespace Cheppers\Robo\Drush\Component;

trait OptionRoot
{
    /**
     * @todo PHP 7.1 type hint.
     *
     * @return null|string
     */
    public function getOptionRoot()
    {
        return $this->drushOptions['root']['value'];
    }

    /**
     * @todo PHP 7.1 type hint.
     *
     * @param null|string $format
     *
     * @return $this
     */
    public function setOptionRoot($format): self
    {
        $this->drushOptions['root']['value'] = $format;

        return $this;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    protected function setOptionsRoot(array $options)
    {
        if (isset($options['root'])) {
            $this->setOptionFormat($options['root']);
        }

        return $this;
    }
}
