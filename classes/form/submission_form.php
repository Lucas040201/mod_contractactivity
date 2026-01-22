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

namespace mod_contractactivity\form;

use moodle_url;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/repository/lib.php');

class submission_form extends \moodleform {
    public function definition() {
        global $USER, $COURSE, $PAGE, $CFG;
        $PAGE->requires->js_call_amd('mod_contractactivity/main', 'mask');
        $PAGE->requires->js_call_amd('mod_contractactivity/main', 'validateFiles');
        $PAGE->requires->js_call_amd('mod_contractactivity/main', 'viaCep');

        $mform = $this->_form;

        $fileopts = [
            'subdirs' => 0,
            'maxbytes' => $COURSE->maxbytes,
            'areamaxbytes' => 10485760,
            'maxfiles' => 2,
            'accepted_types' => ['.png', '.jpg', '.jpeg', '.pdf', '.heic', '.heif'],
            'return_types' => \FILE_INTERNAL | \FILE_INTERNAL
        ];

        $mform->addElement('text', 'fullname', 'Nome completo');
        $mform->setType('fullname', PARAM_TEXT);
        $mform->addRule('fullname', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'nationality', 'Nacionalidade');
        $mform->setType('nationality', PARAM_TEXT);
        $mform->addRule('nationality', 'Campo obrigatório', 'required');

        $mform->addElement('select', 'civilstatus', 'Estado civil', [
            'Solteiro(a)', 'Casado(a)', 'Divorciado(a)', 'Viúvo(a)'
        ]);
        $mform->setType('civilstatus', PARAM_TEXT);
        $mform->addRule('civilstatus', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'profession', 'Profissão');
        $mform->setType('profession', PARAM_TEXT);
        $mform->addRule('profession', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'document', 'Número do RG', ['id' => 'id_document']);
        $mform->setType('document', PARAM_TEXT);
        $mform->addRule('document', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'cpf', 'Número do CPF', ['id' => 'id_cpf']);
        $mform->setType('cpf', PARAM_TEXT);
        $mform->addRule('cpf', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'postal_code', 'CEP', ['id' => 'id_postal_code']);
        $mform->setType('postal_code', PARAM_TEXT);
        $mform->addRule('postal_code', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'address', 'Endereço', ['id' => 'id_address']);
        $mform->setType('address', PARAM_TEXT);
        $mform->addRule('address', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'address_number', 'Número');
        $mform->setType('address_number', PARAM_TEXT);
        $mform->addRule('address_number', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'address_neighbourhood', 'Bairro', ['id' => 'id_neighbourhood']);
        $mform->setType('address_neighbourhood', PARAM_TEXT);
        $mform->addRule('address_neighbourhood', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'address_complement', 'Complemento');
        $mform->setType('address_complement', PARAM_TEXT);

        $mform->addElement('text', 'address_city', 'Cidade', ['id' => 'id_city']);
        $mform->setType('address_city', PARAM_TEXT);
        $mform->addRule('address_city', 'Campo obrigatório', 'required');

        $mform->addElement('filemanager', 'diploma', 'Diploma (frente e verso se houver)', null, $fileopts);
        $mform->addRule('diploma', 'Campo obrigatório', 'required');

        $mform->addElement('filemanager', 'rg_cnh', 'RG ou CNH (frente e verso se houver)', null, $fileopts);
        $mform->addRule('rg_cnh', 'Campo obrigatório', 'required');

        $mform->addElement('filemanager', 'cpf_file', 'CPF (não obrigatório caso já tenha o número do RH ou CNH enviado anteriormente)', null, $fileopts);

        $mform->addElement('filemanager', 'address_proof', 'Comprovante de endereço', null, $fileopts);
        $mform->addRule('address_proof', 'Campo obrigatório', 'required');

        $this->add_action_buttons(true, 'Enviar formulário');
    }

    public function validation($data, $files) {
        $errors = [];

        $areas = ['diploma', 'rg_cnh', 'cpf_file', 'address_proof'];

        $fs = get_file_storage();
        foreach ($areas as $area) {
            $draftid = $data[$area] ?? 0;
            $usercontext = \context_user::instance($GLOBALS['USER']->id);
            $filesinarea = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftid, '', false);

            $filesinareacount = count($filesinarea);

            if ($filesinareacount == 0 && $area === 'diploma') {
                $errors[$area] = 'Envie frente e verso do diploma (se houver).';
            } else if ($area === 'diploma' && $filesinareacount > 2) {
                $errors[$area] = 'Só é possível enviar até 2 arquivos para o campo de diploma.';
            }

            if ($filesinareacount > 1 && $area === 'cpf_file') {
                $errors[$area] = 'Só é possível enviar um arquivo para o campo CPF.';
            }

            if ($filesinareacount == 0 && $area === 'rg_cnh') {
                $errors[$area] = 'Envie frente e verso do documento (se houver).';
            } else if ($area === 'rg_cnh' && $filesinareacount > 2) {
                $errors[$area] = 'Só é possível enviar até 2 arquivos para o campo de documento.';
            }

            if ($filesinareacount == 0 && $area === 'address_proof') {
                $errors[$area] = 'Envie o comprovante de endereço.';
            } else if ($filesinareacount > 1 && $area === 'address_proof') {
                $errors[$area] = 'Só é possível enviar 1 arquivo para o campo de comprovante de endereço.';
            }
        }

        if (!$this->validate_cpf($data['cpf'])) {
            $errors['cpf'] = get_string('invalidcpf', 'mod_contractactivity');
        }

        return $errors;
    }

    private function validate_cpf($cpf) {
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );

        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
}
