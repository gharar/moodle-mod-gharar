<?php

require __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\Util;

Util::forbidNonMoodleAccess();

$plugin->component = 'mod_gharar';
$plugin->version = 2021071603;
$plugin->release = '0.1.0-beta.2';
