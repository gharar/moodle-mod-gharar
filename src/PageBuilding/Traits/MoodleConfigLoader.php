<?php

namespace Gharar\MoodleModGharar\PageBuilding\Traits;

/**
 * @psalm-suppress MissingFile
 */
require_once __DIR__ . "/../../../../../config.php";

/**
 * This is a stub trait that causes the config.php file from Moodle to be
 * loaded. Without it, functions like \required_param() or \optional_param()
 * won't work.
 */
trait MoodleConfigLoader
{
}
