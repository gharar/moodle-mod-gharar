<?php

use mod_gharar\util;
use mod_gharar\moodle_vars;

require_once __DIR__ . '/../../config.php';

$courseModuleId = required_param('id', PARAM_INT);

[$course, $courseModule] = get_course_and_cm_from_cmid($courseModuleId, 'gharar');

$instance = $DB->get_record('gharar', ['id'=> $courseModule->instance], '*', MUST_EXIST);

require_login($course, true, $courseModule);

$PAGE->set_url('/mod/gharar/view.php', ['id' => $courseModuleId]);
$PAGE->set_title($instance->fullname);
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(false);

echo $OUTPUT->header();
echo $OUTPUT->heading(util::get_string('plugin_name_plural'));

echo html_writer::link($instance->name, util::get_string('enter_meeting_link'));

echo $OUTPUT->footer();
