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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/repository/lib.php');

class submission_form extends \moodleform {
    public function definition() {
        global $USER, $COURSE, $PAGE;
        $mform = $this->_form;

        $fileopts = [
            'subdirs' => 0,
            'maxbytes' => 0,
            'maxfiles' => 2,
            'accepted_types' => ['.png', '.jpg', '.jpeg', '.pdf', '.heic', '.heif'],
            'return_types' => \FILE_INTERNAL
        ];

        $mform->addElement('text', 'fullname', 'Nome completo');
        $mform->setType('fullname', PARAM_TEXT);
        $mform->addRule('fullname', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'nationality', 'Nacionalidade');
        $mform->setType('nationality', PARAM_TEXT);
        $mform->addRule('nationality', 'Campo obrigatório', 'required');

        $mform->addElement('select', 'civilstatus', 'Estado civil', [
            'Solteiro', 'Casado', 'Divorciado', 'Viúvo'
        ]);
        $mform->setType('civilstatus', PARAM_TEXT);
        $mform->addRule('civilstatus', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'profession', 'Profissão');
        $mform->setType('profession', PARAM_TEXT);
        $mform->addRule('profession', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'document', 'Número do RG');
        $mform->setType('document', PARAM_TEXT);
        $mform->addRule('document', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'cpf', 'CPF');
        $mform->setType('cpf', PARAM_TEXT);
        $mform->addRule('cpf', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'address', 'Endereço');
        $mform->setType('address', PARAM_TEXT);
        $mform->addRule('address', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'address_number', 'Número');
        $mform->setType('address_number', PARAM_TEXT);
        $mform->addRule('address_number', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'address_neighbourhood', 'Bairro');
        $mform->setType('address_neighbourhood', PARAM_TEXT);
        $mform->addRule('address_neighbourhood', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'address_complement', 'Complemento');
        $mform->setType('address_complement', PARAM_TEXT);

        $mform->addElement('text', 'address_city', 'Cidade');
        $mform->setType('address_city', PARAM_TEXT);
        $mform->addRule('address_city', 'Campo obrigatório', 'required');

        $mform->addElement('text', 'postal_code', 'CEP');
        $mform->setType('postal_code', PARAM_TEXT);
        $mform->addRule('postal_code', 'Campo obrigatório', 'required');

        $mform->addElement('filemanager', 'diploma', 'Diploma (frente e verso)', null, $fileopts);
        $mform->addRule('diploma', 'Campo obrigatório', 'required');

        $mform->addElement('filemanager', 'rg_cnh', 'RG ou CNH (frente e verso)', null, $fileopts);
        $mform->addRule('rg_cnh', 'Campo obrigatório', 'required');

        $mform->addElement('filemanager', 'cpf_file', 'CPF (não obrigatório)', null, $fileopts);

        $mform->addElement('filemanager', 'address_proof', 'Comprovante de endereço', null, $fileopts);
        $mform->addRule('address_proof', 'Campo obrigatório', 'required');

        $this->add_action_buttons(true, 'Enviar formulário');
    }
}
