<?php

namespace MAChitgarha\MoodleModGharar;

use MAChitgarha\MoodleModGharar\Moodle\Globals;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\API;
use MAChitgarha\MoodleModGharar\PageBuilding\AdminSettingsBuilder;
use MAChitgarha\MoodleModGharar\Util;
use Webmozart\Json\JsonDecoder;

/*
 * Defining this global variable is necessary here, because the moodleform_mod
 * file suppose it be available as a global variable. Is there anything worse
 * than globals?
 */
$CFG = Globals::getConfig();
/** @psalm-suppress UnresolvableInclude */
require_once "{$CFG->dirroot}/course/moodleform_mod.php";

/**
 * Form provider for entering the data of a specific module instance.
 *
 * The class is abstract, because if it is instantiated directly, then its
 * parent will complain about the class name, because it must be in the form of:
 * mod_xx_mod_form, where xx is the name of the module (gharar here). That's
 * unfortunate, but unpreventable.
 *
 * @todo Add messages for MoodleQuickForm::addRule() calls, getString method of
 * Util class.
 * @todo Add visual helps (i.e. shown as a question mark in a blue circle) for
 * different inputs.
 */
abstract class InstanceDataForm extends \moodleform_mod
{
    private const FIELD_TYPE_TEXT = "text";
    private const FIELD_TYPE_CHECKBOX = "checkbox";
    private const FIELD_TYPE_ADVANCED_CHECKBOX = "advcheckbox";
    private const FIELD_TYPE_SELECT = "select";

    private const ELEMENT_TYPE_BLOCK = "header";

    private const RULE_TYPE_REQUIRED = "required";
    private const RULE_TYPE_REGEX = "regex";

    private const RULE_VALIDATION_CLIENT = "client";
    private const RULE_VALIDATION_SERVER = "server";

    public const BLOCK_ROOM_SETTINGS_NAME = "room_settings";
    public const BLOCK_RECORDINGS_NAME = "recordings";

    public const FIELD_NAME_NAME = "name";
    private const FIELD_NAME_TYPE = self::FIELD_TYPE_TEXT;
    private const FIELD_NAME_LENGTH = 255;
    private const FIELD_NAME_PARAM_TYPE = \PARAM_TEXT;

    public const FIELD_ROOM_NAME_NAME = "room_name";
    private const FIELD_ROOM_NAME_TYPE = self::FIELD_TYPE_TEXT;
    private const FIELD_ROOM_NAME_LENGTH = 255;
    private const FIELD_ROOM_NAME_PARAM_TYPE = \PARAM_TEXT;

    public const FIELD_IS_PRIVATE_NAME = "is_private";
    private const FIELD_IS_PRIVATE_TYPE = self::FIELD_TYPE_ADVANCED_CHECKBOX;
    private const FIELD_IS_PRIVATE_PARAM_TYPE = \PARAM_BOOL;

    public const FIELD_ROLES_CAN_VIEW_RECORDING_NAME =
        "roles_can_view_recordings";
    private const FIELD_ROLES_CAN_VIEW_RECORDING_TYPE = self::FIELD_TYPE_SELECT;
    private const FIELD_ROLES_CAN_VIEW_RECORDING_PARAM_TYPE = \PARAM_RAW;

    /** @var object|null */
    private $instance = null;

    private static function getFieldString(string $fieldName): string
    {
        return Util::getString("instance_data_form_field_$fieldName");
    }

    private static function getBlockString(string $blockName): string
    {
        return Util::getString("instance_data_form_block_$blockName");
    }

    public function definition(): void
    {
        $this
            ->initInstanceIfUpdating()
            ->addNameField()
            ->addRoomSettingsBlock()
            ->addRecordingsBlock();

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    private function initInstanceIfUpdating(): self
    {
        if ($this->isUpdatingExistingInstance()) {
            $this->instance = Globals::getDatabase()
                ->get_record(
                    Database::TABLE_MAIN,
                    ["id" => $this->_instance],
                    "*",
                    \MUST_EXIST
                );
        }

        return $this;
    }

    private function isUpdatingExistingInstance(): bool
    {
        return !empty($this->_instance);
    }

    private function addNameField(): self
    {
        $this->_form->addElement(
            self::FIELD_NAME_TYPE,
            self::FIELD_NAME_NAME,
            self::getFieldString(self::FIELD_NAME_NAME),
            ["size" => self::FIELD_NAME_LENGTH]
        );

        $this->_form->setType(
            self::FIELD_NAME_NAME,
            self::FIELD_NAME_PARAM_TYPE
        );
        $this->_form->addRule(
            self::FIELD_NAME_NAME,
            null,
            self::RULE_TYPE_REQUIRED,
            null,
            self::RULE_VALIDATION_CLIENT
        );

        return $this;
    }

    private function addRoomSettingsBlock(): self
    {
        $this->_form->addElement(
            self::ELEMENT_TYPE_BLOCK,
            self::BLOCK_ROOM_SETTINGS_NAME,
            self::getBlockString(self::BLOCK_ROOM_SETTINGS_NAME)
        );

        $this->_form->setExpanded(self::BLOCK_ROOM_SETTINGS_NAME);

        return $this
            ->addRoomNameField()
            ->addIsPrivateField();
    }

    private function addRoomNameField(): self
    {
        $this->_form->addElement(
            self::FIELD_ROOM_NAME_TYPE,
            self::FIELD_ROOM_NAME_NAME,
            self::getFieldString(self::FIELD_ROOM_NAME_NAME),
            ["size" => self::FIELD_ROOM_NAME_LENGTH]
        );

        $this->_form->setType(
            self::FIELD_ROOM_NAME_NAME,
            self::FIELD_ROOM_NAME_PARAM_TYPE
        );
        $this->_form->addRule(
            self::FIELD_ROOM_NAME_NAME,
            null,
            self::RULE_TYPE_REQUIRED,
            null,
            self::RULE_VALIDATION_CLIENT
        );

        return $this;
    }

    private function addIsPrivateField(): self
    {
        $this->_form->addElement(
            self::FIELD_IS_PRIVATE_TYPE,
            self::FIELD_IS_PRIVATE_NAME,
            self::getFieldString(self::FIELD_IS_PRIVATE_NAME)
        );

        $this->_form->setType(
            self::FIELD_IS_PRIVATE_NAME,
            self::FIELD_IS_PRIVATE_PARAM_TYPE
        );

        $this->_form->setDefault(
            self::FIELD_IS_PRIVATE_NAME,
            $this->getIsPrivateFieldValue()
        );

        return $this;
    }

    private function getIsPrivateFieldValue(): bool
    {
        if ($this->isUpdatingExistingInstance()) {
            return (new API(
                Util::getConfig(AdminSettingsBuilder::CONFIG_ACCESS_TOKEN_NAME)
            ))
                ->retrieveRoom($this->instance->address)
                ->isPrivate();
        } else {
            // Default value
            return true;
        }
    }

    private function addRecordingsBlock(): self
    {
        $this->_form->addElement(
            self::ELEMENT_TYPE_BLOCK,
            self::BLOCK_RECORDINGS_NAME,
            self::getBlockString(self::BLOCK_RECORDINGS_NAME)
        );

        return $this
            ->addRolesCanViewRecordingsField();
    }

    private function addRolesCanViewRecordingsField(): self
    {
        $select = $this->_form->addElement(
            self::FIELD_ROLES_CAN_VIEW_RECORDING_TYPE,
            self::FIELD_ROLES_CAN_VIEW_RECORDING_NAME,
            self::getFieldString(self::FIELD_ROLES_CAN_VIEW_RECORDING_NAME),
            $this->generateRolesAsHtmlSelectOptions()
        );
        $select->setMultiple(true);

        $this->_form->setType(
            self::FIELD_ROLES_CAN_VIEW_RECORDING_NAME,
            self::FIELD_ROLES_CAN_VIEW_RECORDING_PARAM_TYPE
        );

        $select->setSelected(
            $this->getRolesCanViewRecordingsFieldValue()
        );

        return $this;
    }

    private static function generateRolesAsHtmlSelectOptions(): array
    {
        return array_map(function (object $roleSpec) {
            return $roleSpec->localname;
        }, \role_fix_names(\get_all_roles()));
    }

    private function getRolesCanViewRecordingsFieldValue(): array
    {
        if ($this->isUpdatingExistingInstance()) {
            return (new JsonDecoder())->decode(
                $this->instance->roles_can_view_recordings
            );
        } else {
            /*
             * Default value.
             * Including the roles manager, course creator, editing teacher,
             * teacher, and student.
             */
            return [1, 2, 3, 4, 5];
        }
    }
}
