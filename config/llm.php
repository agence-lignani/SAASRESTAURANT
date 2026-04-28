<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Assistant conversationnel F20 (LLM)
    |--------------------------------------------------------------------------
    |
    | driver = fake : réponses déterministes sans appel réseau (tests / démo hors clé).
    | driver = openai_compat : endpoint style OpenAI (OpenAI, Mistral, Azure OpenAI, etc.).
    |
    */
    'driver' => env('LLM_DRIVER', 'openai_compat'),

    'openai_compat' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_url' => rtrim((string) env('OPENAI_BASE_URL', 'https://api.openai.com/v1'), '/'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'timeout' => (int) env('OPENAI_TIMEOUT', 45),
    ],

    /**
     * Limite minute globale par IP sur l’endpoint chat (§5.8) — complété par quotas par établissement en base.
     */
    'rate_limit_per_minute' => (int) env('LLM_CHAT_RATE_LIMIT_PER_MINUTE', 24),
];
