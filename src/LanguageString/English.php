<?php

namespace MAChitgarha\MoodleModGharar\LanguageString;

/**
 * As a Moodle plugin must have the English language string to be able to be
 * installed, this class exists. But, it is more like a stub file, and it is not
 * regularly updated (as English is not supported).
 */
class English
{
    private const PLUGIN_NAME = "Gharar";
    private const PLUGIN_NAME_PLURAL = self::PLUGIN_NAME . "s";

    public const STRING = [
        // Moodle-specific {
        "modulename" => self::PLUGIN_NAME,
        "pluginname" => self::PLUGIN_NAME,
        "modulenameplural" => self::PLUGIN_NAME_PLURAL,
        // }
    ];
}
