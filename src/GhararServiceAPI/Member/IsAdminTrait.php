<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Member;

trait IsAdminTrait
{
    /** @var bool */
    private $isAdmin;

    private function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }
}
