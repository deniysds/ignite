<?php

return [
    'application_mode' => 'single_application',
    'journal_mode' => 'single',
    'peer_review_model' => 'double_blind',
    'external_reviewer_registration_enabled' => true,
    'publication_formats' => ['pdf', 'html'],
    'issue_assignment_timing' => 'post_acceptance',
    'sla' => [
        'desk_review_days' => 7,
        'review_round_days' => 14,
        'author_revision_days' => 14,
    ],
];
