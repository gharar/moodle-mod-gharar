<?php

namespace MAChitgarha\MoodleModGharar\LanguageString;

/**
 * Although the support for English language is not in priority, and may not be
 * actively maintained (i.e. it may contain empty stub strings), but it is
 * required for language strings of other languages (i.e. Persian here) to work.
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

        "pluginadministration" => self::PLUGIN_NAME . " administration",

        // }

        "plugin_name" => self::PLUGIN_NAME,
        "plugin_name_plural" => self::PLUGIN_NAME_PLURAL,

        "name" => "Name",
        "access_token" => "Access token",
        "room_name" => "Room name",
        "is_private" => "Private",

        "room_settings" => "Room Settings",

        "error_form_access_token_regex" => "",

        "enter_meeting_link" => "Enter",
    ];
}
