<?php

require_once __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\PageBuilding\ViewPageBuilder;

echo (new ViewPageBuilder())
    ->build()
    ->getOutput();
