<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Gharar\MoodleModGharar\{PluginManager, Util};

Util::forbidNonMoodleAccess();

function xmldb_gharar_uninstall(): bool
{
    return PluginManager::uninstall();
}
