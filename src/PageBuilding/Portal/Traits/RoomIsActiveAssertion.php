<?php

namespace Gharar\MoodleModGharar\PageBuilding\Portal\Traits;

use Gharar\MoodleModGharar\LanguageString\StringId;
use Gharar\MoodleModGharar\ServiceApi\Room\AvailableRoom;

trait RoomIsActiveAssertion
{
    private function assertRoomIsActive(AvailableRoom $room): void
    {
        if (!$room->isActive()) {
            throw new \RuntimeException(StringId::ERROR_ROOM_IS_INACTIVE);
        }
    }
}
