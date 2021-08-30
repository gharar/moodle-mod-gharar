<?php

require_once __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\Util;

Util::forbidNonMoodleAccess();

$capabilities = [
    // Ability to add a new Gharar instance
    "mod/gharar:addinstance" => [
        "riskbitmask" => RISK_XSS,
        "captype" => "write",
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
            "manager" => CAP_ALLOW
        ]
    ],
];
