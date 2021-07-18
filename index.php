<?php

use mod_gharar\util;
use mod_gharar\moodle_vars;

require_once __DIR__ . '/../../config.php';

// Get id parameter, using GET (as in GET and POST) method
$courseId = required_param('id', PARAM_INT);

$course = $DB->get_record('course', ['id' => $courseId], '*', MUST_EXIST);

require_login($course);

// TODO: Make these more dynamic, e.g. use get_string for set_title
$PAGE->set_url('/mod/gharar/index.php', ['id' => $courseId]);
$PAGE->set_title(util::get_string('plugin_name_plural'));
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(false);
$PAGE->set_pagelayout('incourse');

$PAGE->navbar->add($PAGE->title, $PAGE->url);

// TODO: Add a rendered to show all the meetings
echo $OUTPUT->header();
echo $OUTPUT->heading(util::get_string('plugin_name_plural'));

// TODO: Wrap it inside a renderable
$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

$table->head = [util::get_string('meeting_name'), util::get_string('meeting_link')];
$table->align = ['center', 'center'];

$instances = moodle_vars::get_instance()->get_database()->get_records('gharar', [
    'course' => $courseId
]);

foreach ($instances as $instace) {
    $row = [
        html_writer::link(new moodle_url('view.php', ['id' => $courseId]), $instance->name),
        $instance->link,
    ];

    $table->data[] = $row;
}

echo html_writer::table($table);

echo $OUTPUT->footer();
