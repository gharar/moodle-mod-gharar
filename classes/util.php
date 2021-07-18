<?php

namespace mod_gharar;

class util
{
    public static function forbid_access_if_not_from_moodle()
    {
        if (!\defined(MOODLE_INTERNAL::class)) {
            exit("Access forbidden");
        }
    }

    public static function get_string($which, ...$otherArgs)
    {
        return get_string($which, plugin::NAME, ...$otherArgs);
    }
}
