<?php
return [
    'default_driver' => \LarAgent\Drivers\OpenAi\OpenAiCompatible::class,
    'default_chat_history' => \LarAgent\History\InMemoryChatHistory::class,
    'default_provider' => 'ollama', // Set default to ollama
    'providers' => [
        'default' => [
            'label' => 'openai',
            'api_key' => env('OPENAI_API_KEY'),
            'default_context_window' => 50000,
            'default_max_completion_tokens' => 10000,
            'default_temperature' => 1,
        ], 
        'ollama' => [
            'label' => 'ollama',
            'model' => 'mistral', // or 'phi3'/'tinyllama' if using those
            'driver' => \LarAgent\Drivers\OpenAi\OpenAiCompatible::class,
            'api_key' => 'ollama',
            'api_url' => 'http://localhost:11434/v1',
            'default_context_window' => 50000,
            'default_max_completion_tokens' => 10000,
            'default_temperature' => 0,
        ],
    ],
];