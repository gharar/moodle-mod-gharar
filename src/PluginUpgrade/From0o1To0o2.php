<?php

namespace MAChitgarha\MoodleModGharar\PluginUpgrade;

use MAChitgarha\MoodleModGharar\GhararServiceAPI\API;

class From0o1To0o2 extends AbstractBase
{
    private const NEW_FIELD_ROOM_NAME = [
        self::FIELD_ATTR_NAME => "room_name",
        self::FIELD_ATTR_TYPE => \XMLDB_TYPE_CHAR,
        self::FIELD_ATTR_PRECISION => 256,
        self::FIELD_ATTR_NOT_NULL => true,
    ];
    private const NEW_FIELD_ADDRESS = [
        self::FIELD_ATTR_NAME => "address",
        self::FIELD_ATTR_TYPE => \XMLDB_TYPE_CHAR,
        self::FIELD_ATTR_PRECISION => 44,
        self::FIELD_ATTR_NOT_NULL => false,
    ];
    private const NEW_FIELD_NEEDS_UPDATE = [
        self::FIELD_ATTR_NAME => "needs_update",
        self::FIELD_ATTR_TYPE => \XMLDB_TYPE_INTEGER,
        self::FIELD_ATTR_PRECISION => 1,
        self::FIELD_ATTR_UNSINGED => true,
        self::FIELD_ATTR_NOT_NULL => true,
        self::FIELD_ATTR_DEFAULT => 0,
    ];

    private const OLD_FIELD_LINK = [
        self::FIELD_ATTR_NAME => "link",
        self::FIELD_ATTR_TYPE => \XMLDB_TYPE_CHAR,
        self::FIELD_ATTR_PRECISION => 512,
        self::FIELD_ATTR_NOT_NULL => true,
        self::FIELD_ATTR_SEQUENCE => false,
    ];

    private const NEW_INDEX_ADDRESS = [
        self::FIELD_INDEX_NAME => "address",
        self::FIELD_INDEX_UNIQUE => true,
    ];

    protected const TABLE_MAIN_NEW_FIELDS = [
        self::NEW_FIELD_ROOM_NAME,
        self::NEW_FIELD_ADDRESS,
        self::NEW_FIELD_NEEDS_UPDATE,
    ];
    protected const TABLE_MAIN_OLD_FIELDS = [
        self::OLD_FIELD_LINK,
    ];

    protected const TABLE_MAIN_NEW_INDEXES = [
        self::NEW_INDEX_ADDRESS,
    ];

    protected function upgradeMainTableRecord(\stdClass $record): \stdClass
    {
        $record->address = $this->extractRoomAddressFromLink($record->link);
        $record->needs_update = $record->address === null;

        $record->room_name = $this->generateRoomNameFromInstanceName(
            $record->name,
            $this->database->get_record(
                "course",
                ["id" => $record->course],
                "fullname",
                \MUST_EXIST
            )->fullname
        );

        return $record;
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

    private static function generateRoomNameFromInstanceName(
        string $instaceName,
        string $courseName
    ): string {
        return "$courseName - $instaceName";
    }
}
