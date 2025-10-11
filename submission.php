<?php
require('../../config.php');

$id = required_param('id', PARAM_INT);
$submissionid = required_param('submissionid', PARAM_INT);

$cm = get_coursemodule_from_id('contractactivity', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
require_login($course, true, $cm);

$context = context_module::instance($cm->id);
$PAGE->set_url('/mod/contractactivity/submission.php', ['id' => $cm->id, 'submissionid' => $submissionid]);
$PAGE->set_title('Envio de Formulário');
$PAGE->set_heading($course->fullname);

$submission = $DB->get_record('contractactivity_submissions', ['id' => $submissionid], '*', MUST_EXIST);

echo $OUTPUT->header();

echo html_writer::tag('h3', 'Sua Submissão');
echo html_writer::start_tag('ul');

$fileareas = ['diploma', 'rg_cnh', 'cpf_file', 'address_proof'];
foreach ($fileareas as $area) {
    $files = $DB->get_records('files', [
        'contextid' => $context->id,
        'component' => 'mod_contractactivity',
        'filearea' => $area,
        'itemid' => $submission->id
    ]);

    echo html_writer::start_tag('li');
    echo html_writer::tag('strong', ucfirst(str_replace('_', ' ', $area)).': ');
    foreach ($files as $file) {
        if ($file->filename !== '.') {
            $url = moodle_url::make_pluginfile_url(
                $context->id,
                'mod_contractactivity',
                $area,
                $submission->id,
                '/',
                $file->filename
            );
            echo html_writer::link($url, $file->filename) . ', ';
        }
    }
    echo html_writer::end_tag('li');
}

echo html_writer::end_tag('ul');
echo $OUTPUT->footer();
