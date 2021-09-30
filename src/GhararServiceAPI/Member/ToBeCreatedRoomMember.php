<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Member;

class ToBeCreatedRoomMember extends AbstractMember
{
    public const PROP_IS_ADMIN = "is_admin";

    /** @var bool */
    private $isAdmin;

    public function __construct(string $phone, bool $isAdmin)
    {
        parent::__construct($phone);
        $this->setIsAdmin($isAdmin);
    }

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
