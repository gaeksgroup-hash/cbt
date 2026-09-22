<?php

return [
    'token_pepper' => env('CBT_TOKEN_PEPPER'),

    'candidate_allowed_statuses' => array_values(
        array_filter(
            array_map(
                'trim',
                explode(
                    ',',
                    (string) env(
                        'CBT_CANDIDATE_ALLOWED_STATUSES',
                        'pre_generated,active'
                    )
                )
            )
        )
    ),
];
