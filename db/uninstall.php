<?php

require_once __DIR__ . "/../vendor/autoload.php";

use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\PluginManager;

Util::forbidNonMoodleAccess();

function xmldb_gharar_uninstall(): bool
{
    return PluginManager::uninstall();
}
