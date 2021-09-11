<?php

require_once __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\PageBuilding\AdminSettingsBuilder;

Util::forbidNonMoodleAccess();

(new AdminSettingsBuilder($settings))
    ->build();
