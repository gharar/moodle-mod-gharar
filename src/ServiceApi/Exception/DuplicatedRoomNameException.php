<?php

namespace Gharar\MoodleModGharar\ServiceApi\Exception;

use Gharar\MoodleModGharar\LanguageString\StringId;
use Gharar\MoodleModGharar\Util;

class DuplicatedRoomNameException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            Util::getString(StringId::ERROR_API_DUPLICATED_ROOM_NAME)
        );
    }
}
