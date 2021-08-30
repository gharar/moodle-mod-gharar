<?php

require_once __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\ViewPageBuilder;

$x = (new ViewPageBuilder())
    ->build()
    ->getOutput();
