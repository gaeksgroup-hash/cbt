<?php

return [
    'token_pepper' => env('CBT_TOKEN_PEPPER'),
    'allow_pre_generated_candidates' => (bool) env('CBT_ALLOW_PRE_GENERATED_CANDIDATES', false),
];
