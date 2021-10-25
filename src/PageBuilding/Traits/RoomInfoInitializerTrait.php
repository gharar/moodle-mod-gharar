<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding\Traits;

use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\AvailableRoom;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\API;

trait RoomInfoInitializerTrait
{
    /** @var AvailableRoom */
    private $roomInfo;

    private function initRoomInfo(API $api, string $roomAddress): self
    {
        $this->roomInfo = $api->retrieveRoom($roomAddress);

        return $this;
    }
}
