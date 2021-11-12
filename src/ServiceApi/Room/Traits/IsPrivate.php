<?php

namespace Gharar\MoodleModGharar\ServiceApi\Room\Traits;

trait IsPrivate
{
    /** @var bool */
    private $isPrivate;

    private function setIsPrivate(bool $isPrivate): self
    {
        $this->isPrivate = $isPrivate;
        return $this;
    }

    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }
}
