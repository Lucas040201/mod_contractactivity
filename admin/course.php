<?php
require('../../../config.php');

$courseid = required_param('courseid', PARAM_INT);
require_login($courseid);
$context = context_course::instance($courseid);
require_capability('mod/contractactivity:viewallsubmissions', $context);

$PAGE->set_url('/mod/contractactivity/admin/course.php', ['courseid' => $courseid]);
$PAGE->set_title('Submissões - Curso');
$PAGE->set_heading('Formulários de Inscrição');

echo $OUTPUT->header();

global $DB;

// Busca instâncias da atividade neste curso
$activity = $DB->get_record('contractactivity', ['course' => $courseid]);

if (!$activity) {
    echo $OUTPUT->notification('Nenhuma atividade encontrada neste curso.', 'error');
    echo $OUTPUT->footer();
    exit;
}

// Busca usuários inscritos no curso
$students = get_enrolled_users($context, 'mod/contractactivity:submit');

// Monta tabela
$table = new html_table();
$table->head = ['Aluno', 'Status', 'Ações'];

foreach ($students as $user) {
    $submission = $DB->get_record('contractactivity_submissions', [
        'formid' => $activity->id,
        'userid' => $user->id
    ]);

    $status = $submission ? 'Enviado' : 'Não enviado';
    $link = $submission
        ? new moodle_url('/mod/contractactivity/admin/submission.php', [
            'submissionid' => $submission->id,
            'courseid' => $courseid
        ])
        : null;

    $actions = $submission ? html_writer::link($link, 'Ver detalhes') : '-';
    $table->data[] = [
        fullname($user),
        $status,
        $actions
    ];
}

echo html_writer::tag('h3', format_string($activity->name));
echo html_writer::table($table);
echo $OUTPUT->footer();
