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

/**
 * @todo Convert to static class instead of a singleton.
 */
class Globals
{
    /** @var ?self */
    private static $instance = null;

    /** @var mysqli_native_moodle_database */
    private $database;
    /** @var stdClass */
    private $config;
    /** @var bootstrap_renderer */
    private $output;
    /** @var moodle_page */
    private $page;
    /** @var admin_root */
    private $adminSettings;

    private function __construct()
    {
        global $DB, $CFG, $OUTPUT, $PAGE, $ADMIN;

        $this->database = $DB;
        $this->config = $CFG;
        $this->output = $OUTPUT;
        $this->page = $PAGE;
        $this->adminSettings = $ADMIN;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getDatabase(): mysqli_native_moodle_database
    {
        return $this->database;
    }

    public function getConfig(): stdClass
    {
        return $this->config;
    }

    public function getOutput(): bootstrap_renderer
    {
        return $this->output;
    }

    public function getPage(): moodle_page
    {
        return $this->page;
    }

    public function getAdminSettings(): admin_root
    {
        return $this->adminSettings;
    }
}
