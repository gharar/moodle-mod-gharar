<?php

namespace MAChitgarha\MoodleModGharar\LanguageString;

class Farsi
{
    private const PLUGIN_NAME = "قرار";
    private const PLUGIN_NAME_PLURAL = self::PLUGIN_NAME . "ها";

    public const STRING = [
        // Moodle-specific {
        "modulename" => self::PLUGIN_NAME,
        "pluginname" => self::PLUGIN_NAME,
        "modulenameplural" => self::PLUGIN_NAME_PLURAL,

        "pluginadministration" => "مدیریت " . self::PLUGIN_NAME,

        // TODO: Add modulename_help
        // }

        "plugin_name" => self::PLUGIN_NAME,
        "plugin_name_plural" => self::PLUGIN_NAME_PLURAL,

        "name" => "نام",
        "access_token" => "توکن دسترسی",
        "room_name" => "نام اتاق",
        "is_private" => "خصوصی بودن",

        "room_settings" => "تنظیمات اتاق",

        "enter_meeting_link" => "ورود",
    ];
}
