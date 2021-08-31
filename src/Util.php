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

    /**
     * @param mixed ...$otherArgs
     */
    public static function getString(string $which, ...$otherArgs): string
    {
        return \get_string($which, Plugin::COMPONENT_NAME, ...$otherArgs);
    }
}
