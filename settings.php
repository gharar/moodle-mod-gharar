<?php

require_once __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\PageBuilding\AdminSettingsBuilder;
use MAChitgarha\MoodleModGharar\Util;

Util::forbidNonMoodleAccess();

(new AdminSettingsBuilder($settings))
    ->build();
