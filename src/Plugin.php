<?php

namespace MAChitgarha\MoodleModGharar;

class Plugin
{
    /** @var string */
    public const MODULE_NAME = "gharar";
    /** @var string */
    public const COMPONENT_NAME = "mod_gharar";

    /** @var string */
    public const DATABASE_MAIN_TABLE_NAME = "gharar";

    /**
     * Path of the plugin, relative to Moodle root directory, without no
     * trailing slashes.
     * @var string
     */
    public const RELATIVE_PATH = "/mod/" . self::MODULE_NAME;

    /** @var string */
    public const CAPABILITIES = [
        // Ability to add a new Gharar instance
        "mod/gharar:addinstance" => [
            "captype" => "write",
            "riskbitmask" => RISK_XSS,
            "contextlevel" => CONTEXT_COURSE,
            "archetypes" => [
                "manager" => CAP_ALLOW,
                "editingteacher" => CAP_ALLOW,
            ],
            "clonepermissionsfrom" => "moodle/course:manageactivities",
        ],

        // Ability to view instances, regardless of the type
        "mod/gharar:view" => [
            "captype" => "read",
            "contextlevel" => CONTEXT_MODULE,
            "archetypes" => [
                "student" => CAP_ALLOW,
                "teacher" => CAP_ALLOW,
                "editingteacher" => CAP_ALLOW,
                "manager" => CAP_ALLOW,
            ],
        ],
    ];
}
