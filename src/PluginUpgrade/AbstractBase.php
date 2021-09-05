<?php

namespace MAChitgarha\MoodleModGharar\PluginUpgrade;

use mysqli_native_moodle_database;
use database_manager;
use xmldb_table;
use xmldb_field;
use MAChitgarha\MoodleModGharar\Database;

/*
 * The class name of children classes are in the following form (wrt semantic
 * versioning):
 *
 * From<OldMajor>o<OldMinor>To<NewMajor>o<NewMinor>
 *
 * o is used between major and minor parts, as a replacement of dots. All of
 * their functions should throw exceptions if anything goes wrong.
 */
abstract class AbstractBase
{
    /** @var mysqli_native_moodle_database */
    private $database;
    /** @var database_manager */
    private $databaseManager;

    /** @var xmldb_table */
    private $mainTable;

    protected const FIELD_ATTR_NAME = "name";
    protected const FIELD_ATTR_TYPE = "type";
    protected const FIELD_ATTR_PRECISION = "precision";
    protected const FIELD_ATTR_UNSINGED = "unsigned";
    protected const FIELD_ATTR_NOT_NULL = "notnull";
    protected const FIELD_ATTR_SEQUENCE = "sequence";
    protected const FIELD_ATTR_DEFAULT = "default";

    /**
     * An array of properties of new fields to be added to the main table. Must
     * be overriden by children classes.
     * @var array[]
     */
    protected const TABLE_MAIN_NEW_FIELDS = [];

    /**
     * An array of properties of old fields to be dropped from the main table.
     * Must be overriden by children classes.
     * @var array[]
     */
    protected const TABLE_MAIN_OLD_FIELDS = [];

    public function __construct()
    {
        $this->database = Globals::getInstance()->getDatabase();
        $this->databaseManager = $this->database->get_manager();
    }

    public function upgrade(): void
    {
        $this->upgradeMainTable();
    }

    protected function upgradeMainTable(): self
    {
        $this
            ->prepareMainTable()
            ->addMainTableNewFields();

        foreach ($this->database->get_records(
            Database::TABLE_MAIN
        ) as $record) {
            $this->database->update_record(
                Database::TABLE_MAIN,
                $this->upgradeMainTableRecord($record)
            );
        }

        return $this
            ->dropMainTableOldFields();
    }

    abstract protected function upgradeMainTableRecord(
        \stdClass $record
    ): \stdClass;

    protected function prepareMainTable(): self
    {
        $this->mainTable = new xmldb_table(Database::TABLE_MAIN);
        return $this;
    }

    protected function addMainTableNewFields(): self
    {
        foreach (static::TABLE_MAIN_NEW_FIELDS as $fieldProps) {
            $this->databaseManager->add_field(
                $this->mainTable,
                new xmldb_field(
                    $fieldProps[self::FIELD_ATTR_NAME],
                    $fieldProps[self::FIELD_ATTR_TYPE],
                    $fieldProps[self::FIELD_ATTR_PRECISION] ?? null,
                    $fieldProps[self::FIELD_ATTR_UNSINGED] ?? null,
                    $fieldProps[self::FIELD_ATTR_NOT_NULL] ?? null,
                    $fieldProps[self::FIELD_ATTR_SEQUENCE] ?? null,
                    $fieldProps[self::FIELD_ATTR_DEFAULT] ?? null
                )
            );
        }

        return $this;
    }

    protected function dropMainTableOldFields(): self
    {
        foreach (static::TABLE_MAIN_OLD_FIELDS as $fieldProps) {
            $this->databaseManager->drop_field(
                $this->mainTable,
                new xmldb_field(
                    $fieldProps[self::FIELD_ATTR_NAME],
                    $fieldProps[self::FIELD_ATTR_TYPE],
                    $fieldProps[self::FIELD_ATTR_PRECISION] ?? null,
                    $fieldProps[self::FIELD_ATTR_UNSINGED] ?? null,
                    $fieldProps[self::FIELD_ATTR_NOT_NULL] ?? null,
                    $fieldProps[self::FIELD_ATTR_SEQUENCE] ?? null,
                    $fieldProps[self::FIELD_ATTR_DEFAULT] ?? null
                )
            );
        }

        return $this;
    }
}
