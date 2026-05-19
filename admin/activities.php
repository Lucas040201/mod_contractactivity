<?php
require('../../../config.php');
require_login();
require_capability('moodle/site:config', context_system::instance());

$courseid = required_param('courseid', PARAM_INT);

$PAGE->set_url('/mod/contractactivity/admin/index.php');
$PAGE->set_title('Atividades do curso');
$PAGE->set_heading('Atividade Formulário de Inscrição');

echo $OUTPUT->header();

global $DB;

$sql = "
SELECT DISTINCT ca.id, ca.name
    FROM mdl_contractactivity ca
inner join mdl_contractactivity_submissions mcs on mcs.formid = ca.id
inner join mdl_course_modules cm on mcs.formid = cm.instance 
inner join mdl_modules m on m.id = cm.module 
    where m.name = 'contractactivity' and cm.course = :courseid;";

$activities = $DB->get_records_sql($sql, ['courseid' => $courseid]);

if (empty($activities)) {
    echo $OUTPUT->notification('Nenhuma atividade encontrada.', 'info');
} else {
    $table = new html_table();
    $table->head = ['Atividade', 'Ações'];

    foreach ($activities as $activity) {
        $link = new moodle_url('/mod/contractactivity/admin/course.php', ['courseid' => $courseid, 'instanceid' => $activity->id]);
        $table->data[] = [
            format_string($activity->name),
            html_writer::link($link, 'Ver inscrições')
        ];
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();
