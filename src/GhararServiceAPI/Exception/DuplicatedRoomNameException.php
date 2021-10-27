<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Exception;

use MAChitgarha\MoodleModGharar\LanguageString\StringId;
use MAChitgarha\MoodleModGharar\Util;

class DuplicatedRoomNameException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            Util::getString(StringId::ERROR_API_DUPLICATED_ROOM_NAME)
        );
    }
}
