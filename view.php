<?php

require_once __DIR__ . "/vendor/autoload.php";

use Gharar\MoodleModGharar\PageBuilding\Visual\ViewPage;

echo (new ViewPage())
    ->build()
    ->getOutput();
