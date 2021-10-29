<?php

namespace Gharar\MoodleModGharar\GhararServiceAPI\Member;

class AvailableRoomMember extends ToBeCreatedRoomMember
{
    public static function fromRawObject(object $object): self
    {
        $member = new self(
            $object->{self::PROP_PHONE},
            $object->{self::PROP_IS_ADMIN}
        );
        $member->setName($object->{self::PROP_NAME});

        return $member;
    }
}
