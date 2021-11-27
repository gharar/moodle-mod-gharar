<?php

namespace Gharar\MoodleModGharar\ServiceApi\Member\Traits;

trait OptionalName
{
    /** @var string|null */
    private $name = null;

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
