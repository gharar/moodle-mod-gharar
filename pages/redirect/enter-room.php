<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use Gharar\MoodleModGharar\PageBuilding\Redirect\EnterRoomPage;

(new EnterRoomPage())
    ->build()
    ->redirect();
