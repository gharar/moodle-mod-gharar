<?php

namespace MAChitgarha\MoodleModGharar;

use MAChitgarha\MoodleModGharar\GhararServiceAPI\API;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\AvailableRoom;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\ToBeCreatedRoom;
use MAChitgarha\MoodleModGharar\Moodle\Globals;
use MAChitgarha\MoodleModGharar\Database;
use MAChitgarha\MoodleModGharar\PageBuilding\AdminSettingsBuilder;

class InstanceManager
{
    /** @var self */
    private static $instance = null;

    /** @var API */
    private $api;

    private function __construct()
    {
        $this->api = new API(
            Util::getConfig(AdminSettingsBuilder::CONFIG_ACCESS_TOKEN_NAME)
        );
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
    public function add(object $instance)
    {
        $room = $api->createRoom(new ToBeCreatedRoom(
            $instance->name,
            $instance->is_private
        ));

        $newRecord = $instance;
        $newRecord->address = $room->getAddress();

        /*
         * Here, if a record with the same "room_name" or "address" exists,
         * because of them being unique fields, an error is occurred.
         */
        $id = Globals::getInstance()
            ->getDatabase()
            ->insert_record(Database::TABLE_MAIN, $newRecord);

        return $id;
    }

    /**
     * @todo Split into more functions.
     */
    public function update(object $instance): bool
    {
        // Important: The id is not stored in the "id" field, but the
        // "instance" one
        $instance->id = $instance->instance;

        $oldRecord = Globals::getInstance()
            ->getDatabase()
            ->get_record(
                Database::TABLE_MAIN,
                ["id" => $instance->id],
                "*",
                \MUST_EXIST
            );

        $room = new AvailableRoom(
            $instance->name,
            $instance->is_private,
            $oldRecord->address
        );

        $room = $this->api->updateRoom($room);

        $newRecord = $instance;
        $newRecord->address = $room->getAddress();

        $result = Globals::getInstance()
            ->getDatabase()
            ->update_record(Database::TABLE_MAIN, $instance);

        return $result;
    }

    public function delete(int $recordId): bool
    {
        $database = Globals::getInstance()->getDatabase();

        $record = $database->get_record(
            Database::TABLE_MAIN,
            ["id" => $recordId]
        );

        if (!$record) {
            return false;
        }

        $deleteResult = $database->delete_records(
            Database::TABLE_MAIN,
            ["id" => $recordId]
        );

        $this->api->destroyRoom(new AvailableRoom());

        return $deleteResult;
    }
}
