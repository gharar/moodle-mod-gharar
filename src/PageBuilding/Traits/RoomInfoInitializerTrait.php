<?php

namespace Gharar\MoodleModGharar\PageBuilding\Traits;

use Gharar\MoodleModGharar\GhararServiceAPI\API;
use Gharar\MoodleModGharar\GhararServiceAPI\Room\AvailableRoom;

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
