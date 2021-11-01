<?php

namespace Gharar\MoodleModGharar;

use Gharar\MoodleModGharar\InstanceForm\{
    FieldType,
    ElementType,
    RuleType,
    RuleValidationWhere,
};
use Gharar\MoodleModGharar\LanguageString\StringId;
use Gharar\MoodleModGharar\Moodle\Globals;
use Gharar\MoodleModGharar\PageBuilding\AdminSettingsBuilder;
use Gharar\MoodleModGharar\ServiceApi\Api;
use Gharar\MoodleModGharar\Util;
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
 * @todo Make use of traits for code re-use.
 */
abstract class InstanceForm extends \moodleform_mod
{
    public const BLOCK_ROOM_SETTINGS_NAME = "room_settings";
    public const BLOCK_RECORDINGS_NAME = "recordings";

    public const FIELD_NAME_NAME = "name";
    private const FIELD_NAME_TYPE = FieldType::TEXT;
    private const FIELD_NAME_LENGTH = 255;
    private const FIELD_NAME_PARAM_TYPE = \PARAM_TEXT;

    private const FIELD_ADDRESS_NAME = "address";
    private const FIELD_ADDRESS_TYPE = FieldType::HIDDEN;
    private const FIELD_ADDRESS_PARAM_TYPE = \PARAM_TEXT;

    public const FIELD_ROOM_NAME_NAME = "room_name";
    private const FIELD_ROOM_NAME_TYPE = FieldType::TEXT;
    private const FIELD_ROOM_NAME_LENGTH = 255;
    private const FIELD_ROOM_NAME_PARAM_TYPE = \PARAM_TEXT;

    public const FIELD_IS_PRIVATE_NAME = "is_private";
    private const FIELD_IS_PRIVATE_TYPE = FieldType::ADVANCED_CHECKBOX;
    private const FIELD_IS_PRIVATE_PARAM_TYPE = \PARAM_BOOL;

    public const FIELD_ROLES_CAN_VIEW_RECORDING_NAME =
        "roles_can_view_recordings";
    private const FIELD_ROLES_CAN_VIEW_RECORDING_TYPE = FieldType::SELECT;
    private const FIELD_ROLES_CAN_VIEW_RECORDING_PARAM_TYPE = \PARAM_RAW;

    private const JS_INSTANCE_FORM_MODULE = "mod_gharar/instance-form";
    private const JS_INSTANCE_FORM_INIT_FUNC = "init";

    /** @var object|null */
    private $instance = null;

    private static function getFieldString(string $fieldName): string
    {
        return Util::getString(
            StringId::FORM_INSTANCE_FIELD_PREFIX . $fieldName
        );
    }

    private static function getBlockString(string $blockName): string
    {
        return Util::getString(
            StringId::FORM_INSTANCE_BLOCK_PREFIX . $blockName
        );
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

        // Pretty hacky, don't touch and don't move it, it has to sit here
        $this->initJsModules();
    }

    private function initInstanceIfUpdating(): self
    {
        if ($this->isUpdatingExistingInstance()) {
            $this->instance = Globals::getDatabase()
                ->get_record(
                    Database\Table::MAIN,
                    ["id" => $this->_instance],
                    "*",
                    \MUST_EXIST
                );
        }

        return $this;
    }

    private function initJsModules(): self
    {
        Globals::getPage()->requires->js_call_amd(
            self::JS_INSTANCE_FORM_MODULE,
            self::JS_INSTANCE_FORM_INIT_FUNC,
            [
                /* @phan-suppress-next-line PhanTypeExpectedObjectPropAccess */
                $this->get_course()->fullname
            ]
        );

        return $this;
    }

    private function isUpdatingExistingInstance(): bool
    {
        return !empty($this->_instance);
    }

    private function makeFieldRequiredAtClient(string $fieldName): self
    {
        $this->_form->addRule(
            $fieldName,
            null,
            RuleType::REQUIRED,
            null,
            RuleValidationWhere::CLIENT
        );

        return $this;
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

        $this->makeFieldRequiredAtClient(self::FIELD_NAME_NAME);

        return $this;
    }

    private function addRoomSettingsBlock(): self
    {
        $this->_form->addElement(
            ElementType::BLOCK,
            self::BLOCK_ROOM_SETTINGS_NAME,
            self::getBlockString(self::BLOCK_ROOM_SETTINGS_NAME)
        );

        $this->_form->setExpanded(self::BLOCK_ROOM_SETTINGS_NAME);

        return $this
            ->addAddressField()
            ->addRoomNameField()
            ->addIsPrivateField();
    }

    private function addAddressField(): self
    {
        $this->_form->addElement(
            self::FIELD_ADDRESS_TYPE,
            self::FIELD_ADDRESS_NAME,
            // Default value is for the case of adding an instance
            $this->instance->address ?? ""
        );

        $this->_form->setType(
            self::FIELD_ADDRESS_NAME,
            self::FIELD_ADDRESS_PARAM_TYPE
        );

        return $this;
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

        $this->makeFieldRequiredAtClient(self::FIELD_ROOM_NAME_NAME);

        $this->_form->setDefault(
            self::FIELD_ROOM_NAME_NAME,
            $this->getRoomNameFieldValue()
        );

        return $this;
    }

    private function getRoomNameFieldValue(): string
    {
        if ($this->isUpdatingExistingInstance()) {
            return $this->instance->room_name;
        } else {
            // Default value
            /* @phan-suppress-next-line PhanTypeExpectedObjectPropAccess */
            return $this->get_course()->fullname . " - ";
        }
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
            return (new Api(
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
            ElementType::BLOCK,
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
        /*
         * As function get_all_roles() return roles in a way that keys represent
         * role IDs, so we rely on that; instead of extracting role IDs by
         * another array_map() and combining the keys and values to generate
         * the result.
         */
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
            return $this->getRolesCanViewRecordingsFieldDefault();
        }
    }

    private static function getRolesCanViewRecordingsFieldDefault(): array
    {
        /*
         * Including the roles manager, course creator, editing teacher,
         * teacher, and student. The values are as strings for keeping
         * consistency with the form itself.
         */
        return ["1", "2", "3", "4", "5"];
    }
}

namespace Gharar\MoodleModGharar\InstanceForm;

class FieldType
{
    public const TEXT = "text";
    public const CHECKBOX = "checkbox";
    public const ADVANCED_CHECKBOX = "advcheckbox";
    public const SELECT = "select";
    public const HIDDEN = "hidden";
}

class ElementType
{
    public const BLOCK = "header";
}

class RuleType
{
    public const REQUIRED = "required";
    public const REGEX = "regex";
}

class RuleValidationWhere
{
    public const CLIENT = "client";
    public const SERVER = "server";
}
