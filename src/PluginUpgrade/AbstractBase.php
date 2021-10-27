<?php

namespace MAChitgarha\MoodleModGharar\PluginUpgrade;

use database_manager;
use MAChitgarha\MoodleModGharar\Database;
use MAChitgarha\MoodleModGharar\Moodle\Globals;
use moodle_database;
use xmldb_field;
use xmldb_index;
use xmldb_table;

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

    protected const FIELD_NAME = "name";
    protected const FIELD_TYPE = "type";
    protected const FIELD_LENGTH = "precision";
    protected const FIELD_UNSINGED = "unsigned";
    protected const FIELD_NOT_NULL = "notnull";
    protected const FIELD_SEQUENCE = "sequence";
    protected const FIELD_DEFAULT = "default";
    protected const FIELD_PREVIOUS = "previous";
    protected const FIELD_NEW_NAME = "new_name";

    protected const INDEX_NAME = "name";
    protected const INDEX_UNIQUE = "unique";
    protected const INDEX_FIELDS = "fields";

    /**
     * An array of properties of new fields to be added to the main table.
     * Should be overriden by children classes.
     * @var array[]
     */
    protected const TABLE_MAIN_NEW_FIELDS = [];
    /**
     * An array of properties of fields to be updated in the main table. Should
     * be overriden by children classes.
     * @var array[]
     */
    protected const TABLE_MAIN_UPDATED_FIELDS = [];
    /**
     * An array of properties of old fields to be dropped from the main table.
     * Should be overriden by children classes.
     * @var array[]
     */
    protected const TABLE_MAIN_OLD_FIELDS = [];

    /**
     * An array of properties of new indexes to be added to the main table.
     * Should be overriden by children classes.
     * @var array[]
     */
    protected const TABLE_MAIN_NEW_INDEXES = [];
    /**
     * An array of properties of old indexes to be dropped from the main table.
     * Should be overriden by children classes.
     * @var array[]
     */
    protected const TABLE_MAIN_OLD_INDEXES = [];

    #[Deprecated]
    protected const FIELD_ATTR_NAME = self::FIELD_NAME;
    #[Deprecated]
    protected const FIELD_ATTR_TYPE = self::FIELD_TYPE;
    #[Deprecated]
    protected const FIELD_ATTR_LENGTH = self::FIELD_LENGTH;
    #[Deprecated]
    protected const FIELD_ATTR_PRECISION = self::FIELD_ATTR_LENGTH;
    #[Deprecated]
    protected const FIELD_ATTR_UNSINGED = self::FIELD_UNSINGED;
    #[Deprecated]
    protected const FIELD_ATTR_NOT_NULL = self::FIELD_NOT_NULL;
    #[Deprecated]
    protected const FIELD_ATTR_SEQUENCE = self::FIELD_SEQUENCE;
    #[Deprecated]
    protected const FIELD_ATTR_DEFAULT = self::FIELD_DEFAULT;

    #[Deprecated]
    protected const FIELD_INDEX_NAME = self::INDEX_NAME;
    #[Deprecated]
    protected const FIELD_INDEX_UNIQUE = self::INDEX_UNIQUE;
    #[Deprecated]
    protected const FIELD_INDEX_FIELDS = self::INDEX_FIELDS;

    public function __construct(moodle_database $database = null)
    {
        $this->database = $database ?? Globals::getDatabase();
        $this->databaseManager = $this->database->get_manager();
    }

    public function upgrade(): void
    {
        $this->upgradeMainTable();
    }

    protected function upgradeMainTable(): self
    {
        return $this
            ->prepareMainTable()
            ->addMainTableNewFields()
            ->updateRecords()
            ->updateMainTableUpdatedFields()
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
            $this->databaseManager->add_field(
                $this->mainTable,
                $this->makeXmldbField($fieldProps)
            );
        }
        return $this;
    }

    protected function updateRecords(): self
    {
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
                // TODO: Do this once, by pushing every item to an array
                $this->database->delete_records(
                    Database::TABLE_MAIN,
                    ["id" => $record->id]
                );
            }
        }

        return $this;
    }

    protected function updateMainTableUpdatedFields(): self
    {
        foreach (static::TABLE_MAIN_UPDATED_FIELDS as $fieldProps) {
            $xmldbField = $this->makeXmldbField($fieldProps);

            if (isset($fieldProps[self::FIELD_NEW_NAME])) {
                $this->databaseManager->rename_field(
                    $this->mainTable,
                    $xmldbField,
                    $fieldProps[self::FIELD_NEW_NAME]
                );
            }

            /*
             * Calling all of the database_manager::change_field_*() functions
             * causes all properties of the field to be updated, not only that
             * particular property. Unfortunately, it is not documented, but
             * we could rely on it, to prevent from multiple redundant change
             * requests and increasing code complexity.
             */
            $this->databaseManager->change_field_type(
                $this->mainTable,
                $xmldbField
            );
        }

        return $this;
    }

    protected function dropMainTableOldFields(): self
    {
        foreach (static::TABLE_MAIN_OLD_FIELDS as $fieldProps) {
            $this->databaseManager->drop_field(
                $this->mainTable,
                $this->makeXmldbField($fieldProps)
            );
        }
        return $this;
    }

    protected function addMainTableNewIndexes(): self
    {
        foreach (static::TABLE_MAIN_NEW_INDEXES as $indexProps) {
            $this->databaseManager->add_index(
                $this->mainTable,
                $this->makeXmldbIndex($indexProps)
            );
        }
        return $this;
    }

    protected function dropMainTableOldIndexes(): self
    {
        foreach (static::TABLE_MAIN_OLD_INDEXES as $indexProps) {
            $this->databaseManager->drop_index(
                $this->mainTable,
                $this->makeXmldbIndex($indexProps)
            );
        }
        return $this;
    }

    private static function makeXmldbField(array $fieldProps): xmldb_field
    {
        return new xmldb_field(
            $fieldProps[self::FIELD_NAME] ?? $fieldProps[self::FIELD_NEW_NAME],
            $fieldProps[self::FIELD_TYPE],
            $fieldProps[self::FIELD_LENGTH] ?? null,
            $fieldProps[self::FIELD_UNSINGED] ?? null,
            $fieldProps[self::FIELD_NOT_NULL] ?? null,
            $fieldProps[self::FIELD_SEQUENCE] ?? null,
            $fieldProps[self::FIELD_DEFAULT] ?? null,
            $fieldProps[self::FIELD_PREVIOUS] ?? null
        );
    }

    private static function makeXmldbIndex(array $indexProps): xmldb_index
    {
        return new xmldb_index(
            $indexProps[self::INDEX_NAME],
            $indexProps[self::INDEX_UNIQUE],
            $indexProps[self::INDEX_FIELDS] ?? [$indexProps[self::INDEX_NAME]]
        );
    }
}
