<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use MAChitgarha\MoodleModGharar\PageBuilding\Redirect\EnterLivePage;

(new EnterLivePage())
    ->build()
    ->redirect();
