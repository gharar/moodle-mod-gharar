<?php

require_once __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\Plugin;
use MAChitgarha\MoodleModGharar\Moodle\Globals;

Util::forbidNonMoodleAccess();

/**
 * @return true|int
 */
function gharar_add_instance(object $record)
{
    $id = Globals::getInstance()
        ->getDatabase()
        ->insert_record(Plugin::DATABASE_MAIN_TABLE_NAME, $record);

    return $id;
}

function gharar_update_instance(object $record): bool
{
    // Important: The id is not stored in the "id" field, but the "instance" one
    $record->id = $record->instance;

    $result = Globals::getInstance()
        ->getDatabase()
        ->update_record(Plugin::DATABASE_MAIN_TABLE_NAME, $record);

    return $result;
}

function gharar_delete_instance(int $recordId): bool
{
    $database = Globals::getInstance()->getDatabase();

    if (
        !$database->get_record(
            Plugin::DATABASE_MAIN_TABLE_NAME,
            ["id" => $recordId]
        ) ||
        !$database->delete_records(
            Plugin::DATABASE_MAIN_TABLE_NAME,
            ["id" => $recordId]
        )
    ) {
        return false;
    }

    return true;
}
