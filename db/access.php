<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'availability/stripepayment:managetransactions' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW
        ]
    ],
];
