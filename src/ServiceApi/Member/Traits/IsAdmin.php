<?php

namespace Gharar\MoodleModGharar\ServiceApi\Member\Traits;

trait IsAdmin
{
    /** @var bool */
    private $isAdmin;

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }
}
