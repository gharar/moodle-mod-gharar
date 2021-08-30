<?php

require __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\Plugin;

Util::forbidNonMoodleAccess();

$plugin->component = Plugin::COMPONENT_NAME;
$plugin->version = 2021071603;
$plugin->release = '0.1.0-beta.2';
