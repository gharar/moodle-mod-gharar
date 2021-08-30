<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use MAChitgarha\MoodleModGharar\Util;

Util::forbidNonMoodleAccess();

const PLUGIN_NAME = "قرار";
const PLUGIN_NAME_PLURAL = PLUGIN_NAME . "ها";

// Moodle-specific
$string["modulename"] = PLUGIN_NAME;
$string["pluginname"] = PLUGIN_NAME;
$string["modulenameplural"] = PLUGIN_NAME_PLURAL;

$string["plugin_name"] = PLUGIN_NAME;
$string["plugin_name_plural"] = PLUGIN_NAME_PLURAL;

$string["meeting_name"] = "نام";
$string["meeting_link"] = "پیوند";

$string["enter_meeting_link"] = "ورود";
