<?php

require_once __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\PageBuilding\Visual\IndexPage;

echo (new IndexPage())
    ->build()
    ->getOutput();
