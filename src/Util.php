<?php

namespace MAChitgarha\MoodleModGharar;

use MAChitgarha\MoodleModGharar\Moodle\Globals;
use core_renderer;

class Util
{
    public static function forbidNonMoodleAccess(): void
    {
        if (!\defined("\MOODLE_INTERNAL")) {
            exit("Access forbidden");
        }
    }

    public static function generateVirtualPhoneNumberFromId(string $id): string
    {
        return "090010" . \str_pad($id, 5, "0", \STR_PAD_LEFT);
    }

    /**
     * @param mixed ...$otherArgs
     */
    public static function getString(string $which, ...$otherArgs): string
    {
        return \get_string($which, Plugin::COMPONENT_NAME, ...$otherArgs);
    }

    /**
     * @todo Throw exceptions in the case of config name not found?
     * @return mixed|false
     */
    public static function getConfig(string $which)
    {
        return \get_config(Plugin::COMPONENT_NAME, $which);
    }

    public static function getPageRenderer(): core_renderer
    {
        return Globals::getPage()->get_renderer("core");
    }
}
