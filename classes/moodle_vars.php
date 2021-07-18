<?php

namespace mod_gharar;

class moodle_vars
{
    private $database;
    private $config;
    private $output;

    public function __construct()
    {
        global $DB, $CFG, $OUTPUT;

        $this->database = $DB;
        $this->config = $CFG;
        $this->output = $OUTPUT;
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
