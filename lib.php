<?php

require_once __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\InstanceManager;

Util::forbidNonMoodleAccess();

/**
 * @return bool|int
 */
function gharar_add_instance(object $record)
{
    return InstanceManager::getInstance()->add($record);
}

function gharar_update_instance(object $record): bool
{
    return InstanceManager::getInstance()->update($record);
}

function gharar_delete_instance(int $recordId): bool
{
    return InstanceManager::getInstance()->delete($recordId);
}
