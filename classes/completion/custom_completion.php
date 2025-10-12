<?php
namespace mod_contractactivity\completion;

use core_completion\activity_custom_completion;

/**
 * Custom completion for mod_contractactivity.
 *
 * @package   mod_contractactivity
 */
class custom_completion extends activity_custom_completion {

    public static function get_defined_custom_rules(): array {
        return ['completiononsubmission'];
    }

    public function get_custom_rule_descriptions(): array {
        return [
            'completiononsubmission' => get_string('completiononsubmission', 'mod_contractactivity'),
        ];
    }

    public function get_state(string $rule): int {
        global $DB, $USER;

        if ($rule === 'completiononsubmission') {
            // Verifica se a regra está ativa nesta instância.
            $enabled = $this->cm->customdata['customcompletionrules']['completiononsubmission'] ?? 0;
            if (empty($enabled)) {
                return COMPLETION_INCOMPLETE;
            }

            // Marca como concluído se o usuário tiver submissão.
            $exists = $DB->record_exists('contractactivity_submissions', [
                'userid' => $USER->id,
                'formid' => $this->cm->instance
            ]);

            return $exists ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
        }

        return COMPLETION_INCOMPLETE;
    }

    public function get_aggregation_method(): int {
        return COMPLETION_AGGREGATION_ALL;
    }

    public function get_sort_order(): array
    {
        return [
            'completiononsubmission'
        ];
    }
}
