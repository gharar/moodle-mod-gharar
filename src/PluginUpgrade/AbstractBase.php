<?php

namespace MAChitgarha\MoodleModGharar\PluginUpgrade;

use mysqli_native_moodle_database;
use database_manager;
use xmldb_table;
use xmldb_field;
use xmldb_index;
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

    protected const FIELD_INDEX_NAME = "name";
    protected const FIELD_INDEX_UNIQUE = "unique";
    protected const FIELD_INDEX_FIELDS = "fields";

    /**
     * An array of properties of new fields to be added to the main table.
     * Must be overriden by children classes.
     * @var array[]
     */
    protected const TABLE_MAIN_NEW_FIELDS = [];
    /**
     * An array of properties of old fields to be dropped from the main table.
     * Must be overriden by children classes.
     * @var array[]
     */
    protected const TABLE_MAIN_OLD_FIELDS = [];

    /**
     * An array of properties of new indexes to be added to the main table.
     * Must be overriden by children classes.
     * @var array[]
     */
    protected const TABLE_MAIN_NEW_INDEXES = [];
    /**
     * An array of properties of old indexes to be dropped from the main table.
     * Must be overriden by children classes.
     * @var array[]
     */
    protected const TABLE_MAIN_OLD_INDEXES = [];

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
            ->addMainTableNewFields()
            ->addMainTableNewIndexes();

        foreach ($this->database->get_records(
            Database::TABLE_MAIN
        ) as $record) {
            $this->database->update_record(
                Database::TABLE_MAIN,
                $this->upgradeMainTableRecord($record)
            );
        }

        return $this
            ->dropMainTableOldFields()
            ->dropMainTableOldIndexes();
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
            $this->performActionOnMainTableForField(
                $fieldProps,
                "add_field"
            );
        }
        return $this;
    }

    protected function dropMainTableOldFields(): self
    {
        foreach (static::TABLE_MAIN_OLD_FIELDS as $fieldProps) {
            $this->performActionOnMainTableForField(
                $fieldProps,
                "drop_field"
            );
        }
        return $this;
    }

    protected function addMainTableNewIndexes(): self
    {
        foreach (static::TABLE_MAIN_NEW_INDEXES as $indexProps) {
            $this->performIndexActionOnMainTableForField(
                $indexProps,
                "add_index"
            );
        }
        return $this;
    }

    protected function dropMainTableOldIndexes(): self
    {
        foreach (static::TABLE_MAIN_OLD_INDEXES as $indexProps) {
            $this->performIndexActionOnMainTableForField(
                $indexProps,
                "drop_index"
            );
        }
        return $this;
    }

    private function performActionOnMainTableForField(
        array $fieldProperties,
        callable $action
    ): void {
        $this->databaseManager->{$action}(
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

    private function performIndexActionOnMainTableForField(
        array $fieldProperties,
        callable $action
    ): void {
        $this->databaseManager->{$action}(
            $this->mainTable,
            new xmldb_index(
                $fieldProps[self::FIELD_INDEX_NAME],
                $fieldProps[self::FIELD_INDEX_UNIQUE],
                $fieldProps[self::FIELD_INDEX_FIELDS] ??
                    [$fieldProps[self::FIELD_INDEX_NAME]]
            )
        );
    }
}
