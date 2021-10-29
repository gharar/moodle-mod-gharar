<?php

namespace Gharar\MoodleModGharar\PluginUpgrade;

class From0o3To0o4 extends AbstractBase
{
    private const NEW_FIELD_INTRO = [
        self::FIELD_NAME => "intro",
        self::FIELD_TYPE => \XMLDB_TYPE_TEXT,
        self::FIELD_NOT_NULL => false,
        self::FIELD_SEQUENCE => false,
        self::FIELD_PREVIOUS => "name",
    ];
    private const NEW_FIELD_INTRO_FORMAT = [
        self::FIELD_NAME => "introformat",
        self::FIELD_TYPE => \XMLDB_TYPE_INTEGER,
        self::FIELD_LENGTH => 4,
        self::FIELD_NOT_NULL => true,
        self::FIELD_DEFAULT => 1,
        self::FIELD_SEQUENCE => false,
        self::FIELD_PREVIOUS => "intro",
    ];
    private const NEW_FIELD_ROLES_CAN_VIEW_RECORDINGS = [
        self::FIELD_NAME => "roles_can_view_recordings",
        self::FIELD_TYPE => \XMLDB_TYPE_TEXT,
        self::FIELD_NOT_NULL => false,
        self::FIELD_SEQUENCE => false,
    ];

    private const UPDATED_FIELD_ROLES_CAN_VIEW_RECORDINGS = [
        self::FIELD_NAME => "roles_can_view_recordings",
        self::FIELD_TYPE => \XMLDB_TYPE_TEXT,
        self::FIELD_NOT_NULL => true,
        self::FIELD_SEQUENCE => false,
    ];

    protected const TABLE_MAIN_NEW_FIELDS = [
        self::NEW_FIELD_INTRO,
        self::NEW_FIELD_INTRO_FORMAT,
        self::NEW_FIELD_ROLES_CAN_VIEW_RECORDINGS,
    ];
    protected const TABLE_MAIN_UPDATED_FIELDS = [
        self::UPDATED_FIELD_ROLES_CAN_VIEW_RECORDINGS,
    ];

    protected function upgradeMainTableRecord(\stdClass $record): array
    {
        /*
         * Hard-coding the default value, because of not relying on other
         * classes that would change any time in the future. By the way, see
         * InstanceDataForm::getRolesCanViewRecordingsFieldDefault().
         */
        $record->roles_can_view_recordings = '["1", "2", "3", "4", "5"]';

        return [true, $record];
    }
}
