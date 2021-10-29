<?php

namespace Gharar\MoodleModGharar\PluginUpgrade;

use Gharar\MoodleModGharar\Database;
use Gharar\MoodleModGharar\GhararServiceAPI\API;

class From0o1To0o2 extends AbstractBase
{
    private const NEW_FIELD_ROOM_NAME = [
        self::FIELD_ATTR_NAME => "room_name",
        self::FIELD_ATTR_TYPE => \XMLDB_TYPE_CHAR,
        self::FIELD_ATTR_PRECISION => 255,
        self::FIELD_ATTR_NOT_NULL => true,
    ];
    private const NEW_FIELD_ADDRESS = [
        self::FIELD_ATTR_NAME => "address",
        self::FIELD_ATTR_TYPE => \XMLDB_TYPE_CHAR,
        self::FIELD_ATTR_PRECISION => 44,
        self::FIELD_ATTR_NOT_NULL => false,
    ];

    private const OLD_FIELD_LINK = [
        self::FIELD_ATTR_NAME => "link",
        self::FIELD_ATTR_TYPE => \XMLDB_TYPE_CHAR,
        self::FIELD_ATTR_PRECISION => 512,
        self::FIELD_ATTR_NOT_NULL => true,
        self::FIELD_ATTR_SEQUENCE => false,
    ];

    private const NEW_INDEX_ROOM_NAME = [
        self::FIELD_INDEX_NAME => "room_name",
        self::FIELD_INDEX_UNIQUE => true,
    ];
    private const NEW_INDEX_ADDRESS = [
        self::FIELD_INDEX_NAME => "address",
        self::FIELD_INDEX_UNIQUE => true,
    ];

    protected const TABLE_MAIN_NEW_FIELDS = [
        self::NEW_FIELD_ROOM_NAME,
        self::NEW_FIELD_ADDRESS,
    ];
    protected const TABLE_MAIN_OLD_FIELDS = [
        self::OLD_FIELD_LINK,
    ];

    protected const TABLE_MAIN_NEW_INDEXES = [
        self::NEW_INDEX_ROOM_NAME,
        self::NEW_INDEX_ADDRESS,
    ];

    protected function upgradeMainTableRecord(\stdClass $record): array
    {
        $record->address = $this->extractRoomAddressFromLink($record->link);

        /*
         * In the following cases, the current record will be removed:
         * - Link is invalid and does not contain any addresses, or
         * - Another record the exact same address is present.
         */
        if (
            $record->address === null ||
            $this->database->record_exists(
                Database::TABLE_MAIN,
                ["address" => $record->address]
            )
        ) {
            return [false, $record];
        }

        $record->room_name = $this->makeRoomNameUnique(
            $this->generateRoomNameFromInstanceName(
                $record->name,
                $this->database->get_record(
                    "course",
                    ["id" => $record->course],
                    "fullname",
                    \MUST_EXIST
                )->fullname
            )
        );

        return [true, $record];
    }

    private static function extractRoomAddressFromLink(string $link): ?string
    {
        $linkPathParts = \explode("/", \parse_url($link, \PHP_URL_PATH));

        foreach ($linkPathParts as $item) {
            if (\preg_match(API::REGEX_ROOM_ADDRESS, $item)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Prepends the course name to instance name to generate the room name.
     */
    private static function generateRoomNameFromInstanceName(
        string $instaceName,
        string $courseName
    ): string {
        return "$courseName - $instaceName";
    }

    private function makeRoomNameUnique(string $roomName): string
    {
        if (!$this->database->record_exists(
            Database::TABLE_MAIN,
            ["room_name" => $roomName]
        )) {
            return $roomName;
        }
        return "$roomName (" . self::generateRandomHex() . ")";
    }

    private static function generateRandomHex(int $length = 20): string
    {
        return \substr(\bin2hex(\random_bytes($length)), 0, $length);
    }
}
