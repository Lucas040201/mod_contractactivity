<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [

    'mod/contractactivity:addinstance' => [
        'riskbitmask' => RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW
        ],
        'clonepermissionsfrom' => 'moodle/course:manageactivities'
    ],

    'mod/contractactivity:submit' => [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'student' => CAP_ALLOW
        ],
    ],

    'mod/contractactivity:viewallsubmissions' => [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher'  => CAP_ALLOW,
            'manager'  => CAP_ALLOW
        ],
    ],

    'mod/contractactivity:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ],
    ],
];
