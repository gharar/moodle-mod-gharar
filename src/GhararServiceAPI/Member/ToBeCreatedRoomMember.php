<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Member;

class ToBeCreatedRoomMember extends AbstractMember
{
    use IsAdminTrait;

    public function __construct(string $phone, bool $isAdmin)
    {
        parent::__construct($phone);
        $this->setIsAdmin($isAdmin);
    }
}
