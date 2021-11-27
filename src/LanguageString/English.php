<?php

namespace Gharar\MoodleModGharar\LanguageString;

/**
 * Although the support for English language is not in priority, and may not be
 * actively maintained (i.e. it may contain stub strings), but it is required
 * for language strings of other languages (e.g. Persian) to work.
 */
class English
{
    /** Stub string that should be implemented in the future. */
    private const NONE = "-";

    private const PLUGIN_NAME = "Gharar";
    private const PLUGIN_NAME_PLURAL = self::PLUGIN_NAME . "s";

    public const STRING = [
        StringId::MODULE_NAME => self::PLUGIN_NAME,
        StringId::PLUGIN_NAME => self::PLUGIN_NAME,
        StringId::MODULE_NAME_PLURAL => self::PLUGIN_NAME_PLURAL,
        StringId::PLUGIN_NAME_PLURAL => self::PLUGIN_NAME_PLURAL,

        StringId::PLUGIN_ADMINISTRATION =>
            self::PLUGIN_NAME . " administration",

        StringId::CAPABILITY_ADD_INSTANCE => self::NONE,
        StringId::CAPABILITY_VIEW_INSTANCE => self::NONE,
        StringId::CAPABILITY_ROOM_ADMIN => self::NONE,
        StringId::CAPABILITY_LIVE_PRESENTER => self::NONE,

        StringId::FORM_INSTANCE_FIELD_NAME => "Name",
        StringId::FORM_INSTANCE_FIELD_ROOM_NAME => "Room name",
        StringId::FORM_INSTANCE_FIELD_IS_PRIVATE => "Private",
        StringId::FORM_INSTANCE_FIELD_ROLES_CAN_VIEW_RECORDINGS =>
            "Visible for (roles)",
        StringId::FORM_INSTANCE_BLOCK_ROOM_SETTINGS => "Room Settings",
        StringId::FORM_INSTANCE_BLOCK_RECORDINGS => "Recordings",

        StringId::CONFIG_ACCESS_TOKEN => "Access token",
        StringId::CONFIG_ACCESS_TOKEN_DESCRIPTION => self::NONE,

        StringId::PAGE_VIEW_ENTER => "Enter",
        StringId::PAGE_VIEW_ENTER_ROOM => self::NONE,
        StringId::PAGE_VIEW_ENTER_LIVE => self::NONE,
        StringId::PAGE_VIEW_NO_RECORDINGS_AVAILABLE => self::NONE,
        StringId::PAGE_VIEW_HEADING_RECORDINGS => self::NONE,

        StringId::ERROR_API_TIMEOUT => self::NONE,
        StringId::ERROR_API_UNAUTHORIZED => self::NONE,
        StringId::ERROR_API_UNHANDLED => self::NONE,
        StringId::ERROR_API_DUPLICATED_ROOM_NAME => self::NONE,

        StringId::ERROR_ROOM_HAS_NO_LIVE => self::NONE,
        StringId::ERROR_ROOM_IS_INACTIVE => self::NONE,

        StringId::ERROR_ACCESS_DENIED => self::NONE,
    ];
}
