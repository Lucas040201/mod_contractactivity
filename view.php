<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version information
 *
 * @package   mod_contractactivity
 * @copyright 2025 Lucas Mendes {@email lucas.mendes.dev@outlook.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once(__DIR__.'/classes/form/submission_form.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('contractactivity', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
$PAGE->set_url('/mod/contractactivity/view.php', ['id' => $cm->id]);
$PAGE->set_title('Formulário de Inscrição');
$PAGE->set_heading($course->fullname);

$form = new \mod_contractactivity\form\submission_form(
    new moodle_url('/mod/contractactivity/view.php', ['id' => $cm->id]),
    ['context' => $context]
);


if ($form->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', ['id' => $course->id]));
} else if ($data = $form->get_data()) {
    global $DB, $USER;

    $recordid = $DB->insert_record('contractactivity_submissions', [
        'userid' => $USER->id,
        'courseid' => $course->id,
        'fullname' => $data->fullname,
        'nationality' => $data->nationality,
        'civilstatus' => $data->civilstatus,
        'profession' => $data->profession,
        'document' => $data->document,
        'cpf' => $data->cpf,
        'address' => $data->address,
        'address_number' => $data->address_number,
        'address_complement' => $data->address_complement,
        'address_neighbourhood' => $data->address_neighbourhood,
        'address_city' => $data->address_city,
        'postal_code' => $data->postal_code,
        'formid' => $cm->instance,
        'timecreated' => time(),
        'timemodified' => time(),
    ]);

    // Salvar arquivos nas fileareas.
    $fileareas = ['diploma', 'rg_cnh', 'cpf_file', 'address_proof'];
    foreach ($fileareas as $area) {
        file_save_draft_area_files(
            $data->{$area},
            $context->id,
            'mod_contractactivity',
            $area,
            $recordid,
            ['subdirs' => 0, 'maxfiles' => 2]
        );
    }

    // Marcar como concluído.
    $completion = new completion_info($course);
    if ($completion->is_enabled($cm)) {
        $completion->update_state($cm, COMPLETION_COMPLETE);
    }

    redirect($PAGE->url, get_string('submissionreceived', 'contractactivity'));
}



$existing = $DB->get_record('contractactivity_submissions', [
    'userid' => $USER->id,
    'formid' => $cm->instance
]);

echo $OUTPUT->header();

if (has_capability('mod/contractactivity:viewallsubmissions', $context)) {
    echo html_writer::link(
        new moodle_url('/mod/contractactivity/admin/course.php', ['courseid' => $course->id]),
        'Ver todos os envios'
    );
} else {
    if ($existing) {
        echo $OUTPUT->notification('Você já enviou este formulário.', 'info');
        echo html_writer::link(
            new moodle_url('/mod/contractactivity/submission.php', [
                'id' => $cm->id,
                'submissionid' => $existing->id
            ]),
            'Ver seu envio'
        );
    } else {

        $form->display();
    }
}



echo $OUTPUT->footer();
