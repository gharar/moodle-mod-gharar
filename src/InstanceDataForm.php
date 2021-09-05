<?php

namespace MAChitgarha\MoodleModGharar;

use MAChitgarha\MoodleModGharar\Moodle\Globals;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\API;
use MAChitgarha\MoodleModGharar\Util;

/*
 * Defining this global variable is necessary here, because the moodleform_mod
 * file suppose it be available as a global variable. Is there anything worse
 * than globals?
 */
$CFG = Globals::getInstance()->getConfig();
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
 */
abstract class InstanceDataForm extends \moodleform_mod
{
    protected const FIELD_TYPE_TEXT = "text";
    protected const FIELD_TYPE_CHECKBOX = "checkbox";

    protected const RULE_TYPE_REQUIRED = "required";
    protected const RULE_TYPE_REGEX = "regex";

    protected const RULE_VALIDATION_CLIENT = "client";
    protected const RULE_VALIDATION_SERVER = "server";

    private const BLOCK_TYPE = "header";
    public const BLOCK_ROOM_SETTINGS_NAME = "room_settings";

    public const FIELD_NAME_NAME = "name";
    private const FIELD_NAME_TYPE = self::FIELD_TYPE_TEXT;
    private const FIELD_NAME_LENGTH = 256;
    private const FIELD_NAME_PARAM_TYPE = \PARAM_TEXT;

    public const FIELD_ROOM_NAME_NAME = "room_name";
    private const FIELD_ROOM_NAME_TYPE = self::FIELD_TYPE_TEXT;
    private const FIELD_ROOM_NAME_LENGTH = 256;
    private const FIELD_ROOM_NAME_PARAM_TYPE = \PARAM_TEXT;

    public const FIELD_IS_PRIVATE_NAME = "is_private";
    private const FIELD_IS_PRIVATE_TYPE = self::FIELD_TYPE_CHECKBOX;
    private const FIELD_IS_PRIVATE_PARAM_TYPE = \PARAM_BOOL;

    public function definition(): void
    {
        $this
            ->addNameField()
            ->addRoomSettingsBlock();

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    private function addNameField(): self
    {
        $this->_form->addElement(
            self::FIELD_NAME_TYPE,
            self::FIELD_NAME_NAME,
            Util::getString("name"),
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
            self::BLOCK_TYPE,
            self::BLOCK_ROOM_SETTINGS_NAME,
            Util::getString("room_settings")
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
            Util::getString("room_name"),
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
            Util::getString("is_private"),
        );

        $this->_form->setType(
            self::FIELD_IS_PRIVATE_NAME,
            self::FIELD_IS_PRIVATE_PARAM_TYPE
        );
        $this->_form->setDefault(
            self::FIELD_IS_PRIVATE_NAME,
            true
        );

        return $this;
    }
}
