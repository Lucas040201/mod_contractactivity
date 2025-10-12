<?php
require('../../../config.php');
require_login();
require_capability('moodle/site:config', context_system::instance());

$PAGE->set_url('/mod/contractactivity/admin/index.php');
$PAGE->set_title('Formulários de Inscrição');
$PAGE->set_heading('Cursos com a atividade Formulário de Inscrição');

echo $OUTPUT->header();

global $DB;

// Lista todos os cursos que possuem a atividade 'contractactivity'.
$sql = "SELECT DISTINCT c.id, c.fullname
        FROM {course} c
        JOIN {course_modules} cm ON cm.course = c.id
        JOIN {modules} m ON m.id = cm.module
        WHERE m.name = 'contractactivity'
        ORDER BY c.fullname ASC";

$courses = $DB->get_records_sql($sql);

if (empty($courses)) {
    echo $OUTPUT->notification('Nenhum curso possui esta atividade.', 'info');
} else {
    $table = new html_table();
    $table->head = ['Curso', 'Ações'];

    foreach ($courses as $course) {
        $link = new moodle_url('/mod/contractactivity/admin/course.php', ['courseid' => $course->id]);
        $table->data[] = [
            format_string($course->fullname),
            html_writer::link($link, 'Ver inscrições')
        ];
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();
