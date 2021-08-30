<?php

require_once __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\Util;

Util::forbidNonMoodleAccess();

const PLUGIN_NAME = "Gharar";

// Moodle-specific
$string["modulename"] = PLUGIN_NAME;
$string["pluginname"] = PLUGIN_NAME;

$string["plugin_name"] = PLUGIN_NAME;
$string["plugin_name_plural"] = PLUGIN_NAME . "s";

$string["meeting_name"] = "Name";
$string["meeting_link"] = "Link";

$string["enter_meeting_link"] = "Enter";
