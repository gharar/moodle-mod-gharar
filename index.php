<?php

require_once __DIR__ . '/../../config.php';

// Get id parameter, using GET (as in GET and POST) method
$courseId = required_param('id', PARAM_INT);

$course = $DB->get_record('course', ['id' => $courseId], '*', MUST_EXIST);

require_login($course);

// TODO: Make these more dynamic, e.g. use get_string for set_title
$PAGE->set_url('/mod/gharar/index.php', ['id' => $courseId]);
$PAGE->set_title('Gharar');
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(false);
$PAGE->set_pagelayout('incourse');

$PAGE->navbar->add($PAGE->title, $PAGE->url);

// TODO: Add a rendered to show all the meetings
echo $OUTPUT->header();
echo $OUTPUT->heading('Gharar meetings');
echo $OUTPUT->footer();
