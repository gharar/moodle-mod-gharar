<?php

require_once __DIR__ . '/../../config.php';

$courseId = required_param('id', PARAM_INT);

$course = $DB->get_record('course', ['id' => $courseId], '*', MUST_EXIST);

require_login($course);

// TODO:
$PAGE->set_url('/mod/gharar/view.php', ['id' => $courseId]);
$PAGE->set_title('Gharar meeting');
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(false);

echo $OUTPUT->header();
echo $OUTPUT->heading('Gharar meeting');
echo $OUTPUT->footer();
