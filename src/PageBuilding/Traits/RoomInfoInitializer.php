<?php

namespace Gharar\MoodleModGharar\PageBuilding\Traits;

use Gharar\MoodleModGharar\ServiceApi\Api;
use Gharar\MoodleModGharar\ServiceApi\Room\AvailableRoom;

trait RoomInfoInitializer
{
    /** @var AvailableRoom */
    private $roomInfo;

    private function initRoomInfo(API $api, string $roomAddress): self
    {
        $this->roomInfo = $api->retrieveRoom($roomAddress);

        return $this;
    }
}
