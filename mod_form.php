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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_contractactivity_mod_form extends moodleform_mod {

    public function definition() {
        $mform = $this->_form;

        // Elementos padrão.
        $this->standard_intro_elements();
        $this->standard_coursemodule_elements();

        // Adiciona as regras de conclusão customizadas.
        $this->add_completion_rules();

        $this->add_action_buttons();
    }

    /**
     * Adiciona checkbox da regra de conclusão personalizada.
     */
    public function add_completion_rules(): array {
        $mform = $this->_form;

        $mform->addElement(
            'checkbox',
            'completiononsubmission',
            '',
            get_string('completiononsubmission', 'mod_contractactivity')
        );
        $mform->setType('completiononsubmission', PARAM_INT);

        return ['completiononsubmission'];
    }

    /**
     * Informa ao Moodle se a regra está habilitada.
     */
    public function completion_rule_enabled($data): bool {
        return !empty($data['completiononsubmission']);
    }

    /**
     * Prepara valores padrão quando edita a atividade.
     */
    public function data_preprocessing(&$default_values) {
        if (!isset($default_values['completiononsubmission'])) {
            $default_values['completiononsubmission'] = 0;
        }
    }
}
