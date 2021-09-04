<?php

namespace MAChitgarha\MoodleModGharar;

use MAChitgarha\MoodleModGharar\Moodle\Util;
use MAChitgarha\MoodleModGharar\Moodle\Globals;

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
 * mod_xx_mod_form, which xx is the name of the module (gharar here). That's
 * unfortunate, but unpreventable.
 */
abstract class InstanceDataForm extends \moodleform_mod
{
    private const FIELD_NAME_NAME = "name";
    private const FIELD_NAME_TYPE = "text";
    private const FIELD_NAME_LENGTH = 256;

    private const FIELD_LINK_NAME = "link";
    private const FIELD_LINK_TYPE = "text";
    private const FIELD_LINK_LENGTH = 512;

    public function definition(): void
    {
        $this
            ->addNameField()
            ->addLinkField();

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    private function addNameField(): self
    {
        $this->_form->addElement(
            self::FIELD_NAME_TYPE,
            self::FIELD_NAME_NAME,
            Util::getString("meeting_name"),
            ["size" => self::FIELD_NAME_LENGTH],
        );

        $this->_form->setType(
            self::FIELD_NAME_NAME,
            \PARAM_TEXT,
        );
        $this->_form->addRule(
            self::FIELD_NAME_NAME,
            null,
            "required",
            null,
            "client",
        );

        return $this;
    }

    private function addLinkField(): self
    {
        $this->_form->addElement(
            self::FIELD_LINK_TYPE,
            self::FIELD_LINK_NAME,
            Util::getString("meeting_link"),
            ["size" => self::FIELD_NAME_LENGTH],
        );

        $this->_form->setType(
            self::FIELD_LINK_NAME,
            \PARAM_TEXT,
        );
        $this->_form->addRule(
            self::FIELD_LINK_NAME,
            null,
            "required",
            null,
            "client",
        );

        return $this;
    }
}
