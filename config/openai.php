<?php
return [
    // ... other services
    'ollama' => [
        'api_key' => env('OLLAMA_API_KEY', ''), // Optional, Ollama usually doesn't require an API key
        'base_url' => env('OLLAMA_API_BASE_URL', 'http://localhost:11434/v1'),
    ],
];
