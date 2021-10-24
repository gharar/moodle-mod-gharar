<?php

namespace MAChitgarha\MoodleModGharar\LanguageString;

class StringId
{
    public const MODULE_NAME = "modulename";
    public const PLUGIN_NAME = "pluginname";
    public const MODULE_NAME_PLURAL = "modulenameplural";
    public const PLUGIN_NAME_PLURAL = "pluginnameplural";

    public const PLUGIN_ADMINISTRATION = "pluginadministration";

    public const CAPABILITY_ADD_INSTANCE = "gharar:addinstance";
    public const CAPABILITY_VIEW_INSTANCE = "gharar:view";
    public const CAPABILITY_ROOM_ADMIN = "gharar:room_admin";

    public const FORM_INSTANCE_FIELD_NAME =
        "form_instance_field_name";
    public const FORM_INSTANCE_FIELD_ROOM_NAME =
        "form_instance_field_room_name";
    public const FORM_INSTANCE_FIELD_IS_PRIVATE =
        "form_instance_field_is_private";
    public const FORM_INSTANCE_FIELD_ROLES_CAN_VIEW_RECORDINGS =
        "form_instance_field_roles_can_view_recordings";
    public const FORM_INSTANCE_BLOCK_ROOM_SETTINGS =
        "form_instance_block_room_settings";
    public const FORM_INSTANCE_BLOCK_RECORDINGS =
        "form_instance_block_recordings";

    public const PAGE_VIEW_ENTER_ROOM =
        "page_view_enter_room";
    public const PAGE_VIEW_ENTER_ROOM_HAVING_LIVE =
        "page_view_enter_room_having_live";
    public const PAGE_VIEW_ENTER_LIVE =
        "page_view_enter_live";

    public const CONFIG_ACCESS_TOKEN = "config_access_token";
    public const CONFIG_ACCESS_TOKEN_DESCRIPTION = "config_access_token_desc";

    public const ERROR_API_TIMEOUT = "error_api_timeout";
    public const ERROR_API_UNAUTHORIZED = "error_api_unauthorized";
    public const ERROR_API_UNHANDLED = "error_api_unhandled";
    public const ERROR_API_DUPLICATED_ROOM_NAME =
        "error_api_duplicated_room_name";
}
