<?php

namespace Cheppers\Robo\Drush\Component;

trait OptionFormat
{
    /**
     * @todo PHP 7.1 type hint.
     *
     * @return null|string
     */
    public function getOptionFormat()
    {
        return $this->drushOptions['format']['value'];
    }

    /**
     * @todo PHP 7.1 type hint.
     *
     * @param null|string $format
     *
     * @return $this
     */
    public function setOptionFormat($format): self
    {
        $this->drushOptions['format']['value'] = $format;

        return $this;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    protected function setOptionsFormat(array $options)
    {
        if (isset($options['format'])) {
            $this->setOptionFormat($options['format']);
        }

        return $this;
    }
}
