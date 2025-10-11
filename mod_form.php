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

        // Nome e descrição padrão.
        $this->standard_intro_elements();

        // Configuração simples: nenhum campo adicional.
        $this->standard_coursemodule_elements();

        $this->add_completion_rules();

        $this->add_action_buttons();
    }

    public function add_completion_rules() {
        $mform = $this->_form;

        $group = [];
        $group[] = $mform->createElement('checkbox', 'completiononsubmit', '',
            get_string('completiononsubmit', 'contractactivity'));
        $mform->addGroup($group, 'completionoptions', get_string('completion', 'contractactivity'), [' '], false);
        $mform->addHelpButton('completionoptions', 'completiononsubmit', 'contractactivity');

        return ['completiononsubmit'];
    }

    public function completion_rule_enabled($data) {
        return !empty($data['completiononsubmit']);
    }
}
