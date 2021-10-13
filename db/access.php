<?php

require_once __DIR__ . "/../vendor/autoload.php";

use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\Capability;

Util::forbidNonMoodleAccess();

$capabilities = Capability::DEFINITIONS;
