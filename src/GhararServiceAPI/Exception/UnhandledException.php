<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Exception;

use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\LanguageString\StringId;

class UnhandledException extends Exception
{
    public function __construct(string $message, int $code = -1)
    {
        parent::__construct(
            Util::getString(
                StringId::ERROR_API_UNHANDLED,
                (object)[
                    "message" => $message,
                    "statusCode" => $code,
                ]
            )
        );
    }
}
