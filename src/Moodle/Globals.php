<?php

namespace MAChitgarha\MoodleModGharar\Moodle;

// For using $CFG global
require_once __DIR__ . "/../../../../config.php";

class Globals
{
    private static $instance = null;

    /** @var mysqli_native_moodle_database */
    private $database;
    /** @var \stdClass */
    private $config;
    /** @var \bootstrap_renderer */
    private $output;
    /** @var \moodle_page */
    private $page;

    private function __construct()
    {
        global $DB, $CFG, $OUTPUT, $PAGE;

        $this->database = $DB;
        $this->config = $CFG;
        $this->output = $OUTPUT;
        $this->page = $PAGE;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getDatabase(): \mysqli_native_moodle_database
    {
        return $this->database;
    }

    public function getConfig(): \stdClass
    {
        return $this->config;
    }

    public function getOutput(): \bootstrap_renderer
    {
        return $this->output;
    }

    public function getPage(): \moodle_page
    {
        return $this->page;
    }
}