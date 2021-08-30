<?php

namespace MAChitgarha\MoodleModGharar;

require_once "{$CFG->dirroot}/course/moodleform_mod.php";

class InstanceDataForm extends \moodleform_mod
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
