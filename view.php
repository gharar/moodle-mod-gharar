<?php

use mod_gharar\util;
use mod_gharar\moodle_vars;

require_once __DIR__ . '/../../config.php';

$courseId = required_param('id', PARAM_INT);

$course = $DB->get_record('course', ['id' => $courseId], '*', MUST_EXIST);

require_login($course);

$PAGE->set_url('/mod/gharar/view.php', ['id' => $courseId]);
$PAGE->set_title(util::get_string('plugin_name_plural'));
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(false);

echo $OUTPUT->header();
echo $OUTPUT->heading(util::get_string('plugin_name_plural'));

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

$table->head = [util::get_string('meeting_name')];
$table->align = ['center'];

$instances = moodle_vars::get_instance()->get_database()->get_records('gharar', [
    'course' => $courseId
]);

foreach ($instances as $instace) {
    $row = [
        html_writer::link($instance->name, $instance->link)
    ];

    $table->data[] = $row;
}

echo html_writer::table($table);

echo $OUTPUT->footer();
