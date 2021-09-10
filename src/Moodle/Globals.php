<?php

namespace MAChitgarha\MoodleModGharar\Moodle;

use mysqli_native_moodle_database;
use stdClass;
use bootstrap_renderer;
use moodle_page;
use admin_root;

/**
 * For using $CFG global.
 * @psalm-suppress MissingFile
 */
require_once __DIR__ . "/../../../../config.php";

class Globals
{
    private function __construct()
    {
    }

    public static function getDatabase(): mysqli_native_moodle_database
    {
        global $DB;
        return $DB;
    }

    public static function getConfig(): stdClass
    {
        global $CFG;
        return $CFG;
    }

    public static function getOutput(): bootstrap_renderer
    {
        global $OUTPUT;
        return $OUTPUT;
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
