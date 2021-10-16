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

        "gharar:addinstance" => "-",
        "gharar:view" => "-",
        "gharar:room_admin" => "-",
        // }

        "plugin_name" => self::PLUGIN_NAME,
        "plugin_name_plural" => self::PLUGIN_NAME_PLURAL,

        "instance_data_form_field_name" => "Name",
        "instance_data_form_field_room_name" => "Room name",
        "instance_data_form_field_is_private" => "Private",
        "instance_data_form_field_roles_can_view_recordings" =>
            "Show for (roles)",
        "instance_data_form_block_room_settings" => "Room Settings",
        "instance_data_form_block_recordings" => "Recordings",

        "access_token" => "Access token",
        "access_token_description" => "-",

        "enter_room" => "Enter the virtual class",
        "enter_live" => "-",

        "error_api_request_timeout" => "-",
        "error_api_request_unauthorized" => "-",
        "error_api_request_unhandled" => "-",
        "error_api_request_duplicated_room_name" => "-",
    ];
}
