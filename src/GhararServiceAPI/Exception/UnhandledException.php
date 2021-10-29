<?php

namespace Gharar\MoodleModGharar\GhararServiceAPI\Exception;

use Gharar\MoodleModGharar\LanguageString\StringId;
use Gharar\MoodleModGharar\Util;

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
