<?php

namespace Gharar\MoodleModGharar\ServiceApi\Room\Traits;

trait Name
{
    /** @var string */
    private $name;

    private function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
