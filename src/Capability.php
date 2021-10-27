<?php

namespace MAChitgarha\MoodleModGharar;

use const CAP_ALLOW;
use const CONTEXT_COURSE;
use const CONTEXT_MODULE;
use const RISK_XSS;

class Capability
{
    public const ADD_INSTANCE = "mod/gharar:addinstance";
    public const VIEW_INSTANCE = "mod/gharar:view";
    public const ROOM_ADMIN = "mod/gharar:room_admin";

    public const DEFINITIONS = [
        self::ADD_INSTANCE => [
            Property::CAP_TYPE => CapabilityType::WRITE,
            Property::RISK_BITMASK => RISK_XSS,
            Property::CONTEXT_LEVEL => CONTEXT_COURSE,
            Property::ARCHE_TYPES => [
                Role::MANAGER => CAP_ALLOW,
                Role::COURSE_CREATOR => CAP_ALLOW,
                Role::EDITING_TEACHER => CAP_ALLOW,
            ],
            Property::CLONE_PERMISSIONS_FROM =>
                "moodle/course:manageactivities",
        ],

        self::VIEW_INSTANCE => [
            Property::CAP_TYPE => CapabilityType::READ,
            Property::CONTEXT_LEVEL => CONTEXT_MODULE,
            Property::ARCHE_TYPES => [
                Role::MANAGER => CAP_ALLOW,
                Role::COURSE_CREATOR => CAP_ALLOW,
                Role::EDITING_TEACHER => CAP_ALLOW,
                Role::TEACHER => CAP_ALLOW,
                Role::STUDENT => CAP_ALLOW,
            ],
        ],

        self::ROOM_ADMIN => [
            Property::CAP_TYPE => CapabilityType::READ,
            Property::CONTEXT_LEVEL => CONTEXT_MODULE,
            Property::ARCHE_TYPES => [
                Role::MANAGER => CAP_ALLOW,
                Role::EDITING_TEACHER => CAP_ALLOW,
                Role::TEACHER => CAP_ALLOW,
            ]
        ],
    ];
}

class Property
{
    public const CAP_TYPE = "captype";
    public const RISK_BITMASK = "riskbitmask";
    public const CONTEXT_LEVEL = "contextlevel";
    public const ARCHE_TYPES = "archetypes";
    public const CLONE_PERMISSIONS_FROM = "clonepermissionsfrom";
}

class CapabilityType
{
    public const WRITE = "write";
    public const READ = "read";
}

class Role
{
    public const MANAGER = "manager";
    public const COURSE_CREATOR = "coursecreator";
    public const EDITING_TEACHER = "editingteacher";
    public const TEACHER = "teacher";
    public const STUDENT = "student";
    public const GUEST = "guest";
}
