<?php

namespace Gharar\MoodleModGharar\ServiceApi\Member;

class AvailableRoomMember implements
    Interfaces\AvailableMember
{
    use Traits\Phone {
        setPhone as private;
    }
    use Traits\IsAdmin {
        setIsAdmin as private;
    }
    use Traits\OptionalName;

    public function __construct(string $phone, bool $isAdmin)
    {
        $this
            ->setPhone($phone)
            ->setIsAdmin($isAdmin);
    }
}
