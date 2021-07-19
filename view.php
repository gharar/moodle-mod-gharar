<?php

use mod_gharar\util;
use mod_gharar\moodle_vars;

require_once __DIR__ . '/../../config.php';

$courseModuleId = required_param('id', PARAM_INT);

[$course, $courseModule] = get_course_and_cm_from_cmid($courseModuleId, 'gharar');

$instance = $DB->get_record('gharar', ['id'=> $courseModule->instance], '*', MUST_EXIST);

require_login($course, true, $courseModule);

$PAGE->set_url('/mod/gharar/view.php', ['id' => $courseModuleId]);
$PAGE->set_title("$course->shortname: $instance->name");
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(false);

echo $OUTPUT->header();
echo $OUTPUT->heading($instance->name);

echo html_writer::link($instance->link, util::get_string('enter_meeting_link'));

echo $OUTPUT->footer();
