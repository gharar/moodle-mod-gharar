<?php

namespace Gharar\MoodleModGharar;

use Gharar\MoodleModGharar\Moodle\Globals;
use Gharar\MoodleModGharar\ServiceApi\Api;
use Gharar\MoodleModGharar\ServiceApi\Room\{
    AvailableRoom,
    PossibleRoom
};
use stdClass;

class InstanceManager
{
    use Traits\ApiInitializer;

    /** @var ?self */
    private static $instance = null;

    private function __construct()
    {
        $this->initApi();
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
        if (!$this->isSetAddress($instance)) {
            $room = $this->createNewRoom($instance);
            $instance->address = $room->getAddress();
        }

        $this->setRolesCanViewRecordings($instance);

        /*
         * Here, if a record with the same "room_name" or "address" exists,
         * because of them being unique fields, an error is occurred.
         */
        $id = Globals::getDatabase()
            ->insert_record(Database\Table::MAIN, $instance);

        return $id;
    }

    private function isSetAddress(object $instance): bool
    {
        return (bool)(\preg_match(API::REGEX_ROOM_ADDRESS, $instance->address));
    }

    private function createNewRoom(object $instance): AvailableRoom
    {
        return $this->api->createRoom(new PossibleRoom(
            $instance->room_name,
            $instance->is_private
        ));
    }

    private function setRolesCanViewRecordings(stdClass $instance): void
    {
        $instance->roles_can_view_recordings = Util::jsonEncode(
            $instance->roles_can_view_recordings_select
        );
    }

    /**
     * @todo Split into more functions.
     */
    public function update(object $instance): bool
    {
        /*
         * Important: The id is not stored in the "id" field, but the
         * "instance" one.
         */
        $instance->id = $instance->instance;

        $room = new AvailableRoom(
            $instance->room_name,
            $instance->address
        );
        $room->setIsPrivate($instance->is_private);
        $room->setIsActive(true);

        $room = $this->api->updateRoom($room);

        $this->setRolesCanViewRecordings($instance);
        $this->unsetIntroFormat($instance);

        $result = Globals::getDatabase()
            ->update_record(Database\Table::MAIN, $instance);

        return $result;
    }

    private function unsetIntroFormat(stdClass $instance): void
    {
        unset($instance->introformat);
    }

    /**
     * @todo Fix removing an instance not removing its associated room using the
     * API. Maybe we should use events?
     */
    public function delete(int $recordId): bool
    {
        $database = Globals::getDatabase();

        $record = $database->get_record(
            Database\Table::MAIN,
            ["id" => $recordId]
        );

        if (!$record) {
            return false;
        }

        $deleteResult = $database->delete_records(
            Database\Table::MAIN,
            ["id" => $recordId]
        );

        /*
         * We don't destroy the room, as the user may have recordings on it, or
         * simply need it. In the future, a confirmation make be taken from the
         * user to do so.
         */

        return $deleteResult;
    }
}
