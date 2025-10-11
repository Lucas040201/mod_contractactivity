<?php
defined('MOODLE_INTERNAL') || die();

function contractactivity_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO: return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE: return false;
        default: return null;
    }
}

/**
 * Retorna as regras de conclusão disponíveis para esta atividade.
 */
function contractactivity_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    // Verifica se o usuário tem um envio salvo.
    $exists = $DB->record_exists('contractactivity_submissions', [
        'formid' => $cm->instance,
        'userid' => $userid
    ]);

    // Retorna verdadeiro se a submissão existir (atividade concluída)
    return $exists;
}

function contractactivity_get_file_areas($course, $cm, $context) {
    return [
        'diploma' => 'Diploma frente e verso',
        'rg_cnh' => 'RG ou CNH',
        'cpf_file' => 'CPF',
        'address_proof' => 'Comprovante de endereço'
    ];
}


/**
 * Adiciona uma nova instância da atividade "Formulário de Inscrição".
 */
function contractactivity_add_instance($data, $mform) {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = time();

    // Insere no banco a nova instância da atividade.
    return $DB->insert_record('contractactivity', $data);
}

/**
 * Atualiza uma instância existente.
 */
function contractactivity_update_instance($data, $mform) {
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;

    return $DB->update_record('contractactivity', $data);
}

/**
 * Exclui uma instância da atividade.
 */
function contractactivity_delete_instance($id) {
    global $DB;

    if (!$record = $DB->get_record('contractactivity', ['id' => $id])) {
        return false;
    }

    // Remove o registro principal e os envios associados.
    $DB->delete_records('contractactivity_submissions', ['formid' => $record->id]);
    $DB->delete_records('contractactivity', ['id' => $record->id]);

    return true;
}

function contractactivity_extend_settings_navigation($settingsnav, navigation_node $node) {
    global $PAGE;

    $context = $PAGE->context;

    if (has_capability('mod/contractactivity:viewallsubmissions', $context)) {
        $url = new moodle_url('/mod/contractactivity/admin/index.php');
        $node->add(
            get_string('pluginadministration', 'contractactivity'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            null,
            new pix_icon('i/settings', '')
        );
    }
}

