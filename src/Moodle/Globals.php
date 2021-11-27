<?php

namespace Gharar\MoodleModGharar\Moodle;

use admin_root;
use moodle_database;
use moodle_page;
use stdClass;

/**
 * For using $CFG global (and possibly other globals as well).
 * @psalm-suppress MissingFile
 */
require_once __DIR__ . "/../../../../config.php";

class Globals
{
    public static function getDatabase(): moodle_database
    {
        global $DB;
        return $DB;
    }

    public static function getConfig(): stdClass
    {
        global $CFG;
        return $CFG;
    }

    public static function getPage(): moodle_page
    {
        global $PAGE;
        return $PAGE;
    }

    public static function getAdminSettings(): admin_root
    {
        global $ADMIN;
        return $ADMIN;
    }

    public static function getUser(): stdClass
    {
        global $USER;
        return $USER;
    }
}
