<?php

require_once __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\Util;

Util::forbidNonMoodleAccess();

$plugin->component = "mod_gharar";
$plugin->version = 2021071604;
$plugin->release = "0.1.0-beta.3";
$plugin->maturity = MATURITY_BETA;

// Minimum Moodle version is 3.9.0
$plugin->requires = 2020061500;
$plugin->supported = [39, 311];
