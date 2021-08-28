<?php

namespace MAChitgarha\MoodleModGharar\Moodle;

class Globals
{
    private static $instance = null;

    private $database;
    private $config;
    private $output;

    private function __construct()
    {
        global $DB, $CFG, $OUTPUT;

        $this->database = $DB;
        $this->config = $CFG;
        $this->output = $OUTPUT;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getDatabase()
    {
        return $this->database;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getOutput()
    {
        return $this->output;
    }
}
