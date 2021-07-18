<?php

namespace mod_gharar;

class moodle_vars
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

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get_database()
    {
        return $this->database;
    }

    public function get_config()
    {
        return $this->config;
    }

    public function get_output()
    {
        return $this->output;
    }
}
