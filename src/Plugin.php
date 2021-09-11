<?php

namespace MAChitgarha\MoodleModGharar;

use const RISK_XSS;
use const CAP_ALLOW;
use const CONTEXT_COURSE;
use const CONTEXT_MODULE;

class Plugin
{
    public const MODULE_NAME = "gharar";
    public const COMPONENT_NAME = "mod_gharar";

    /**
     * Path of the plugin, relative to Moodle root directory, without no
     * trailing slashes.
     */
    public const RELATIVE_PATH = "/mod/" . self::MODULE_NAME;

    public const CAPABILITY_ADD_INSTANCE =
        "mod/gharar:addinstance";
    public const CAPABILITY_VIEW_INSTANCE =
        "mod/gharar:view";
    public const CAPABILITY_ENTER_ROOM_AS_ADMIN =
        "mod/gharar:enter_room_as_admin";

    public const CAPABILITIES = [
        self::CAPABILITY_ADD_INSTANCE => [
            "captype" => "write",
            "riskbitmask" => RISK_XSS,
            "contextlevel" => CONTEXT_COURSE,
            "archetypes" => [
                "manager" => CAP_ALLOW,
                "editingteacher" => CAP_ALLOW,
            ],
            "clonepermissionsfrom" => "moodle/course:manageactivities",
        ],

        self::CAPABILITY_VIEW_INSTANCE => [
            "captype" => "read",
            "contextlevel" => CONTEXT_MODULE,
            "archetypes" => [
                "manager" => CAP_ALLOW,
                "editingteacher" => CAP_ALLOW,
                "teacher" => CAP_ALLOW,
                "student" => CAP_ALLOW,
            ],
        ],

        self::CAPABILITY_ENTER_ROOM_AS_ADMIN => [
            "captype" => "read",
            "contextlevel" => CONTEXT_MODULE,
            "archetypes" => [
                "manager" => CAP_ALLOW,
                "editingteacher" => CAP_ALLOW,
                "teacher" => CAP_ALLOW,
            ]
        ],
    ];
}
