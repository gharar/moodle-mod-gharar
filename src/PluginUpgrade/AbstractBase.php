<?php

namespace MAChitgarha\MoodleModGharar\PluginUpgrade;

use moodle_database;
use database_manager;
use xmldb_table;
use xmldb_field;
use xmldb_index;
use MAChitgarha\MoodleModGharar\Database;
use MAChitgarha\MoodleModGharar\Moodle\Globals;

/*
 * The class name of children classes are in the following form (wrt semantic
 * versioning):
 *
 * From<OldMajor>o<OldMinor>To<NewMajor>o<NewMinor>
 *
 * o is used between major and minor parts, as a replacement of dots. All of
 * their functions should throw exceptions if anything goes wrong.
 *
 * Note that, everything in the children classes must remain hard-coded, and not
 * grabbed from other classes; simply because classes change frequently and
 * should not be relied on.
 */
abstract class AbstractBase
{
    /** @var moodle_database */
    protected $database;
    /** @var database_manager */
    protected $databaseManager;

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
        $this->database = Globals::getDatabase();
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
            [$keepIt, $record] = $this->upgradeMainTableRecord($record);

            if ($keepIt) {
                $this->database->update_record(
                    Database::TABLE_MAIN,
                    $record
                );
            } else {
                $this->database->delete_records(
                    Database::TABLE_MAIN,
                    ["id" => $record->id]
                );
            }
        }

        return $this
            ->dropMainTableOldIndexes()
            ->dropMainTableOldFields()
            ->addMainTableNewIndexes();
    }

    /**
     * @return array A pair. The first element is whether to keep the record or
     * not, the second is the updated record.
     */
    abstract protected function upgradeMainTableRecord(
        \stdClass $record
    ): array;

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
        array $fieldProps,
        string $databaseManagerActionMethod
    ): void {
        $this->databaseManager->{$databaseManagerActionMethod}(
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
        array $fieldProps,
        string $databaseManagerActionMethod
    ): void {
        $this->databaseManager->{$databaseManagerActionMethod}(
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
