<?php
global $CFG, $USER;
require('../../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once(__DIR__.'/../classes/form/submission_form.php');

$submissionid = required_param('submissionid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

$submission = $DB->get_record('contractactivity_submissions', ['id' => $submissionid], '*', MUST_EXIST);
$activity = $DB->get_record('contractactivity', ['id' => $submission->formid], '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('contractactivity', $activity->id, $courseid);
$context = context_module::instance($cm->id);

require_login($courseid);
require_capability('mod/contractactivity:viewallsubmissions', $context);

$PAGE->set_url('/mod/contractactivity/admin/submission.php', ['submissionid' => $submissionid, 'courseid' => $courseid]);
$PAGE->set_title('Submissão de ' . fullname($DB->get_record('user', ['id' => $submission->userid])));
$PAGE->set_heading('Editar ou visualizar submissão');

echo $OUTPUT->header();

echo html_writer::link(
    new moodle_url('/mod/contractactivity/admin/generatepdf.php', ['id' => $submission->id]),
    get_string('generatecontract', 'mod_contractactivity'),
    ['class' => 'btn btn-primary', 'target' => '_blank']
);

$form = new \mod_contractactivity\form\submission_form(
    new moodle_url('/mod/contractactivity/admin/submission.php', ['submissionid' => $submissionid, 'courseid' => $courseid]),
    ['context' => $context]
);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/mod/contractactivity/admin/course.php', ['courseid' => $courseid]));
} else if ($data = $form->get_data()) {
    $data->id = $submissionid;
    $DB->update_record('contractactivity_submissions', $data);

    // Atualiza arquivos
    $fileareas = ['diploma', 'rg_cnh', 'cpf_file', 'address_proof'];
    foreach ($fileareas as $area) {
        file_save_draft_area_files(
            $data->{$area},
            $context->id,
            'mod_contractactivity',
            $area,
            $submissionid,
            ['subdirs' => 0, 'maxfiles' => 2]
        );
    }

    redirect($PAGE->url, 'Submissão atualizada com sucesso!');
}

$fileareas = ['diploma', 'rg_cnh', 'cpf_file', 'address_proof'];
$usercontext = context_user::instance($USER->id);
foreach ($fileareas as $area) {
    $draftitemid = file_get_submitted_draft_itemid($area);

    file_prepare_draft_area(
        $draftitemid,
        $context->id,
        'mod_contractactivity',
        $area,
        $submissionid,
        ['subdirs' => 0, 'maxfiles' => 2],
        $usercontext->id
    );

    $submission->{$area} = $draftitemid;
}

// Preenche formulário com dados existentes
$form->set_data($submission);
$form->display();

// Exibir arquivos enviados
echo html_writer::tag('h3', 'Arquivos Enviados');
$fileareas = ['diploma', 'rg_cnh', 'cpf_file', 'address_proof'];

foreach ($fileareas as $area) {
    $files = $DB->get_records('files', [
        'contextid' => $context->id,
        'component' => 'mod_contractactivity',
        'filearea' => $area,
        'itemid' => $submissionid
    ]);

    echo html_writer::tag('strong', ucfirst(str_replace('_', ' ', $area)) . ': ');
    if (empty($files)) {
        echo 'Nenhum arquivo enviado.<br>';
    } else {
        foreach ($files as $file) {
            if ($file->filename !== '.') {
                $url = moodle_url::make_pluginfile_url(
                    $context->id,
                    'mod_contractactivity',
                    $area,
                    $submissionid,
                    '/',
                    $file->filename
                );
                echo html_writer::link($url, $file->filename) . '<br>';
            }
        }
    }
    echo '<hr>';
}

echo $OUTPUT->footer();
