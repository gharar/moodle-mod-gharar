<?php

namespace Gharar\MoodleModGharar;

class Plugin
{
    public const MODULE_NAME = "gharar";
    public const COMPONENT_NAME = "mod_gharar";

    /**
     * Path of the plugin, relative to Moodle root directory, without trailing
     * slashes.
     */
    public const RELATIVE_PATH = "/mod/" . self::MODULE_NAME;
}
