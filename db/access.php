<?php

require_once __DIR__ . "/../vendor/autoload.php";

use MAChitgarha\MoodleModGharar\{Capability, Util};

Util::forbidNonMoodleAccess();

$capabilities = Capability::DEFINITIONS;
