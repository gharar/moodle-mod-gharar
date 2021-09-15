<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Exception;

use MAChitgarha\MoodleModGharar\Util;

class UnhandledException extends Exception
{
    public function __construct(string $message, int $code = -1)
    {
        parent::__construct(
            Util::getString(
                "error_api_request_unhandled",
                (object)[
                    "message" => $message,
                    "statusCode" => $code,
                ]
            )
        );
    }
}
