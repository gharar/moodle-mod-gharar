<?php

namespace Gharar\MoodleModGharar\ServiceApi\Member;

class Mapper
{
    public static function possibleLiveMemberToAvailableLiveMember(
        PossibleLiveMember $liveMember
    ): AvailableLiveMember {
        return (new AvailableLiveMember(
            $liveMember->getPhone()
        ))
            ->setName($liveMember->getName());
    }

    public static function possibleRoomMemberToAvailableRoomMember(
        PossibleRoomMember $roomMember
    ): AvailableRoomMember {
        return (new AvailableRoomMember(
            $roomMember->getPhone(),
            $roomMember->isAdmin()
        ))
            ->setName($roomMember->getName());
    }
}
