<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Exception;

use MAChitgarha\MoodleModGharar\Util;

class UnauthorizedException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            Util::getString("error_api_request_unauthorized")
        );
    }
}
