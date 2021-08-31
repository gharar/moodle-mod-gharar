<?php

namespace MAChitgarha\MoodleModGharar\LanguageString;

class English
{
    private const PLUGIN_NAME = "Gharar";
    private const PLUGIN_NAME_PLURAL = self::PLUGIN_NAME . "s";

    public const STRING = [
        // Moodle-specific {
        "modulename" => self::PLUGIN_NAME,
        "pluginname" => self::PLUGIN_NAME,
        "modulenameplural" => self::PLUGIN_NAME_PLURAL,

        "pluginadministration" => self::PLUGIN_NAME . " administration",
        // }

        "plugin_name" => self::PLUGIN_NAME,
        "plugin_name_plural" => self::PLUGIN_NAME_PLURAL,

        "meeting_name" => "Name",
        "meeting_link" => "Link",

        "enter_meeting_link" => "Enter",
    ];
}
