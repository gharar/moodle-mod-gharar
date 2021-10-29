<?php

require_once __DIR__ . "/vendor/autoload.php";

use Gharar\MoodleModGharar\PageBuilding\AdminSettingsBuilder;
use Gharar\MoodleModGharar\Util;

Util::forbidNonMoodleAccess();

(new AdminSettingsBuilder($settings))
    ->build();
