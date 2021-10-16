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
    /** @var ?self */
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
     * @return bool|int
     */
    public function add(object $instance)
    {
        /*
         * One can inject room address into the hidden address field in data
         * form to make use of existing room, instead of creating a new one.
         */
        if (!$this->instanceHasAddress($instance)) {
            $room = $this->createNewRoomFromInstance($instance);
            $instance->address = $room->getAddress();
        }

        $instance->roles_can_view_recordings = json_encode(
            $instance->roles_can_view_recordings
        );

        /*
         * Here, if a record with the same "room_name" or "address" exists,
         * because of them being unique fields, an error is occurred.
         */
        $id = Globals::getDatabase()
            ->insert_record(Database::TABLE_MAIN, $instance);

        return $id;
    }

    private function instanceHasAddress(object $instance): bool
    {
        return (bool)(\preg_match(API::REGEX_ROOM_ADDRESS, $instance->address));
    }

    private function createNewRoomFromInstance(object $instance): AvailableRoom
    {
        return $this->api->createRoom(new ToBeCreatedRoom(
            $instance->room_name,
            $instance->is_private
        ));
    }

    /**
     * @todo Split into more functions.
     */
    public function update(object $instance): bool
    {
        // Important: The id is not stored in the "id" field, but the
        // "instance" one
        $instance->id = $instance->instance;

        $oldRecord = Globals::getDatabase()
            ->get_record(
                Database::TABLE_MAIN,
                ["id" => $instance->id],
                "*",
                \MUST_EXIST
            );

        $room = new AvailableRoom(
            $instance->room_name,
            $oldRecord->address
        );
        $room->setIsPrivate($instance->is_private);
        $room->setIsActive(true);

        $room = $this->api->updateRoom($room);

        $newRecord = $instance;
        $newRecord->address = $room->getAddress();

        $result = Globals::getDatabase()
            ->update_record(Database::TABLE_MAIN, $instance);

        return $result;
    }

    /**
     * @todo Fix removing an instance not removing its associated room using the
     * API. Maybe we should use events?
     */
    public function delete(int $recordId): bool
    {
        $database = Globals::getDatabase();

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

        $this->api->destroyRoom($record->address);

        return $deleteResult;
    }
}
