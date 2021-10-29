<?php

namespace Gharar\MoodleModGharar\GhararServiceAPI\Member;

class AvailableLiveMember extends ToBeCreatedLiveMember
{
    /**
     * @return static
     */
    public static function fromRawObject(object $object)
    {
        $member = new static(
            $object->{self::PROP_PHONE}
        );
        $member->setName($object->{self::PROP_NAME});

        return $member;
    }
}
