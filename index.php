<?php

require_once __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\IndexPageBuilder;

echo (new IndexPageBuilder())
    ->build()
    ->getOutput();
