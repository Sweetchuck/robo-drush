<?php

namespace Cheppers\Robo\Drush\Component;

trait Arguments
{
    public function getArguments(): array
    {
        return $this->drushArguments;
    }

    public function setArguments(array $arguments): self
    {
        $this->drushArguments = $arguments;

        return $this;
    }
}
