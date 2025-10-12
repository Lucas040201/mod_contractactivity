<?php
defined('MOODLE_INTERNAL') || die();

function contractactivity_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
        case FEATURE_COMPLETION_HAS_RULES:
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

function contractactivity_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    $exists = $DB->record_exists('contractactivity_submissions', [
        'formid' => $cm->instance,
        'userid' => $userid
    ]);

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

function contractactivity_add_instance($data, $mform) {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = time();

    return $DB->insert_record('contractactivity', $data);
}

function contractactivity_update_instance($data, $mform) {
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;

    return $DB->update_record('contractactivity', $data);
}

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

function contractactivity_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    global $CFG;

    require_login($course, true, $cm);

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    $validareas = ['diploma', 'rg_cnh', 'cpf_file', 'address_proof'];
    if (!in_array($filearea, $validareas)) {
        send_file_not_found();
    }

    $itemid = (int)array_shift($args);
    if (empty($args)) {
        send_file_not_found();
    }

    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_contractactivity', $filearea, $itemid, $filepath, $filename);

    if (!$file || $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}

function contractactivity_get_completion_active_rule_descriptions($cm): array {
    if (empty($cm->customdata['customcompletionrules']) || $cm->completion != COMPLETION_TRACKING_AUTOMATIC) {
        return [];
    }

    $descriptions = [];
    if (!empty($cm->customdata['customcompletionrules']['completiononsubmission'])) {
        $descriptions[] = get_string('completiononsubmission', 'mod_contractactivity');
    }

    return $descriptions;
}

function contractactivity_get_coursemodule_info($coursemodule) {
    global $DB;

    $record = $DB->get_record('contractactivity', ['id' => $coursemodule->instance], 'id, name, intro, introformat, completiononsubmission', MUST_EXIST);

    $info = new cached_cm_info();
    $info->name = $record->name;

    if ($coursemodule->showdescription) {
        $info->content = format_module_intro('contractactivity', $record, $coursemodule->id, false);
    }

    // Preenche a regra customizada, apenas se o acompanhamento automático estiver ativo.
    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        $info->customdata['customcompletionrules']['completiononsubmission'] = $record->completiononsubmission;
    }

    return $info;
}
