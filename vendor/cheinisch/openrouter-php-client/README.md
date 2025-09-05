# OpenRouter PHP Client

Lightweight PHP client for the OpenRouter.ai API. Minimal, and easy to use.

## Requirements

* PHP >= 8.1
* Composer
* guzzlehttp/guzzle (installed automatically)

## Installation

`composer require cheinisch/openrouter-php-client`

## Usage

1) Minimal (static convenience method)
```
<?php
require __DIR__.'/vendor/autoload.php';

use OpenRouter\Client;

$apiKey = getenv('OPENROUTER_API_KEY') ?: 'sk-or-...';
echo Client::OpenRouterChat($apiKey, 'openai/gpt-4o-mini', 'Say only: OK');
```

2) With optional attribution headers (Referer / Title)
```
<?php
require __DIR__.'/vendor/autoload.php';

use OpenRouter\Client;

$apiKey  = getenv('OPENROUTER_API_KEY');
$model   = 'mistralai/mistral-small';
$prompt  = 'Give me one short fun fact about PHP.';
$referer = 'https://example.com'; // optional
$title   = 'My PHP App';               // optional

echo Client::OpenRouterChat($apiKey, $model, $prompt, $referer, $title);
```
> Note: The static method OpenRouterChat($apiKey, $model, $prompt, ?$referer = null, ?$title = null) is a wrapper for chat(...) and returns only the plain answer string.

## Available Language Models

* anthropic/claude-3.5-sonnet
* google/gemini-2.5-flash
* mistralai/mistral-small
* openai/gpt-4o-mini
* x-ai/grok-3-mini
* ... and a lot more