<?php

namespace MAChitgarha\MoodleModGharar;

class Util
{
    public static function forbidNonMoodleAccess(): void
    {
        if (!\defined("\MOODLE_INTERNAL")) {
            exit("Access forbidden");
        }
    }

    public static function getString($which, ...$otherArgs)
    {
        return get_string($which, plugin::NAME, ...$otherArgs);
    }
}
