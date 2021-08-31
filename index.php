<?php

require_once __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\PageBuilding\IndexPageBuilder;

echo (new IndexPageBuilder())
    ->build()
    ->getOutput();
