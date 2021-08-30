<?php

require __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\ViewPageBuilder;

$x = (new ViewPageBuilder())
    ->build()
    ->getOutput();
