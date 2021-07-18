<?php

use \mod_gharar\util;
use \mod_gharar\moodle_vars;

util::forbid_access_if_not_from_moodle();

require_once "{$CFG->dirroot}/course/moodleform_mod.php";

class mod_gharar_mod_form extends moodleform_mod
{
    private $moodle;

    public function __construct($current, $section, $courseModule, $course)
    {
        $this->moodle = new moodle_vars();

        parent::__construct($current, $section, $courseModule, $course);
    }

    public function definition()
    {
        $this->add_name_field();
        $this->add_link_field();
    }

    private function add_name_field()
    {
        $this->_form->addElement('text', 'name', util::get_string(
            'meeting_name'
        ), ['size' => 256]);
        $this->_form->setType('name', PARAM_TEXT);
        $this->_form->addRule('name', null, 'required', null, 'client');
    }

    private function add_link_field()
    {
        $this->_form->addElement('text', 'link', util::get_string(
            'meeting_link'
        ), ['size' => 512]);
        $this->_form->setType('link', PARAM_TEXT);
        $this->_form->addRule('name', null, 'required', null, 'client');
    }
}
