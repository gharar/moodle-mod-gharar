<?php

require_once __DIR__ . "/../vendor/autoload.php";

use MAChitgarha\MoodleModGharar\{PluginManager, Util};

Util::forbidNonMoodleAccess();

function xmldb_gharar_upgrade(int $oldVersion = 0): bool
{
    return PluginManager::upgrade($oldVersion);
}
