<?php

namespace MAChitgarha\MoodleModGharar;

use MAChitgarha\MoodleModGharar\GhararServiceAPI\API;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\ToBeCreatedRoom;
use MAChitgarha\MoodleModGharar\Moodle\Globals;
use MAChitgarha\MoodleModGharar\Database;

class InstanceManager
{
    /** @var self */
    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return true|int
     */
    public function add(object $record)
    {
        $api = new API($record->access_token);

        $room = $api->createRoom(new ToBeCreatedRoom(
            $record->name,
            $record->is_private
        ));

        $record->address = $room->getAddress();

        /*
         * Here, if a record with the same "room_name" or "address" exists,
         * because of them being unique fields, an error is occurred.
         */
        $id = Globals::getInstance()
            ->getDatabase()
            ->insert_record(Database::TABLE_MAIN, $record);

        return $id;
    }

    public function update(object $record): bool
    {
        // Important: The id is not stored in the "id" field, but the
        // "instance" one
        $record->id = $record->instance;

        $result = Globals::getInstance()
            ->getDatabase()
            ->update_record(Database::TABLE_MAIN, $record);

        return $result;
    }

    public function delete(int $recordId): bool
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
