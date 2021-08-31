<?php

namespace MAChitgarha\MoodleModGharar;

class Plugin
{
    public const MODULE_NAME = "gharar";
    public const COMPONENT_NAME = "mod_gharar";

    public const DATABASE_MAIN_TABLE_NAME = "gharar";

    /**
     * Path of the plugin, relative to Moodle root directory, without no
     * trailing slashes.
     * @var string
     */
    public const RELATIVE_PATH = "/mod/" . self::MODULE_NAME;
}
