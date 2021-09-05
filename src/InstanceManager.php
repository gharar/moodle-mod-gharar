<?php

namespace MAChitgarha\MoodleModGharar;

use MAChitgarha\MoodleModGharar\Moodle\Globals;
use MAChitgarha\MoodleModGharar\Database;

class InstanceManager
{
    /**
     * @return true|int
     */
    public static function add(object $record)
    {
        $id = Globals::getInstance()
            ->getDatabase()
            ->insert_record(Database::TABLE_MAIN, $record);

        return $id;
    }

    public static function update(object $record): bool
    {
        // Important: The id is not stored in the "id" field, but the
        // "instance" one
        $record->id = $record->instance;

        $result = Globals::getInstance()
            ->getDatabase()
            ->update_record(Database::TABLE_MAIN, $record);

        return $result;
    }

    public static function delete(int $recordId): bool
    {
        $database = Globals::getInstance()->getDatabase();

        if (
            !$database->get_record(
                Database::TABLE_MAIN,
                ["id" => $recordId]
            ) ||
            !$database->delete_records(
                Database::TABLE_MAIN,
                ["id" => $recordId]
            )
        ) {
            return false;
        }

        return true;
    }
}
