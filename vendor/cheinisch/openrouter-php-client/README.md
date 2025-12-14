# OpenRouter PHP Client

It’s a lightweight PHP client for the OpenRouter.ai API that lets you call chat models with a single static method or a simple client, returning the plain text answer. It supports optional attribution headers (Referer/Title), works with many models (e.g., GPT-4o-mini, Claude 3.5, Gemini 2.5, Mistral), and installs via Composer.

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

use cheinisch\OpenRouterClient;

$apiKey = getenv('OPENROUTER_API_KEY') ?: 'sk-or-...';
echo Client::OpenRouterChat($apiKey, 'openai/gpt-4o-mini', 'Say only: OK');
```

2) With optional attribution headers (Referer / Title)
```
<?php
require __DIR__.'/vendor/autoload.php';

use cheinisch\OpenRouterClient;

$apiKey  = getenv('OPENROUTER_API_KEY');
$model   = 'mistralai/mistral-small';
$prompt  = 'Give me one short fun fact about PHP.';
$referer = 'https://example.com'; // optional
$title   = 'My PHP App';               // optional

echo OpenRouterClient::OpenRouterChat($apiKey, $model, $prompt, $referer, $title);
```
> Note: The static method OpenRouterChat($apiKey, $model, $prompt, ?$referer = null, ?$title = null) is a wrapper for chat(...) and returns only the plain answer string.

## Available Language Models

* anthropic/claude-3.5-sonnet
* google/gemini-2.5-flash
* mistralai/mistral-small
* openai/gpt-4o-mini
* x-ai/grok-3-mini
* ... and a lot more
