<?php

namespace Gharar\MoodleModGharar\ServiceApi\Member;

class ToBeCreatedRoomMember
{
    use Traits\Phone;
    use Traits\IsAdmin;
    use Traits\OptionalName;

    public function __construct(string $phone, bool $isAdmin)
    {
        $this
            ->setPhone($phone)
            ->setIsAdmin($isAdmin);
    }
}
