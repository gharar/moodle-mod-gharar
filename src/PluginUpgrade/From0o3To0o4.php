<?php

namespace MAChitgarha\MoodleModGharar\PluginUpgrade;

use Webmozart\Json\JsonEncoder;

class From0o3To0o4 extends AbstractBase
{
    private const NEW_FIELD_INTRO = [
        self::FIELD_ATTR_NAME => "intro",
        self::FIELD_ATTR_TYPE => \XMLDB_TYPE_TEXT,
        self::FIELD_ATTR_NOT_NULL => false,
        self::FIELD_ATTR_SEQUENCE => false,
    ];
    private const NEW_FIELD_INTRO_FORMAT = [
        self::FIELD_ATTR_NAME => "introformat",
        self::FIELD_ATTR_TYPE => \XMLDB_TYPE_INTEGER,
        self::FIELD_ATTR_LENGTH => 4,
        self::FIELD_ATTR_NOT_NULL => true,
        self::FIELD_ATTR_DEFAULT => 1,
        self::FIELD_ATTR_SEQUENCE => false,
    ];
    private const NEW_FIELD_ROLES_CAN_VIEW_RECORDINGS = [
        self::FIELD_ATTR_NAME => "roles_can_view_recordings",
        self::FIELD_ATTR_TYPE => \XMLDB_TYPE_TEXT,
        self::FIELD_ATTR_NOT_NULL => true,
        self::FIELD_ATTR_SEQUENCE => false,
    ];

    protected const TABLE_MAIN_NEW_FIELDS = [
        self::NEW_FIELD_INTRO,
        self::NEW_FIELD_INTRO_FORMAT,
        self::NEW_FIELD_ROLES_CAN_VIEW_RECORDINGS,
    ];

    protected function upgradeMainTableRecord(\stdClass $record): array
    {
        $record->roles_can_view_recordings = (new JsonEncoder())->encode([]);

        return [true, $record];
    }
}
