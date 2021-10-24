<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Exception;

use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\LanguageString\StringId;

class TimeoutException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            Util::getString(StringId::ERROR_API_TIMEOUT)
        );
    }
}
