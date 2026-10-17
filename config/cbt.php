<?php

return [
    'token_pepper' => env('CBT_TOKEN_PEPPER'),
    'allow_pre_generated_candidates' => (bool) env('CBT_ALLOW_PRE_GENERATED_CANDIDATES', false),
    'preview_only' => (bool) env('CBT_PREVIEW_ONLY', false),
    'gdp_entitlements' => [
        'url' => env('GDP_ENTITLEMENT_URL', 'https://gdp.gaeks.com/api/cbt-entitlements.php'),
        'secret' => env('GDP_ENTITLEMENT_SECRET'),
        'timeout_seconds' => (int) env('GDP_ENTITLEMENT_TIMEOUT_SECONDS', 8),
        'session_ttl_minutes' => (int) env('GDP_ENTITLEMENT_SESSION_TTL_MINUTES', 120),
    ],
];
