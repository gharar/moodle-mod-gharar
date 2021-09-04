<?php

require_once __DIR__ . "/../vendor/autoload.php";

use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\Plugin;

Util::forbidNonMoodleAccess();

$capabilities = Plugin::CAPABILITIES;
