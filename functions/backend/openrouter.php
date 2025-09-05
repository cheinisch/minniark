<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once __DIR__ . '/../../vendor/autoload.php';

    use OpenRouter\Client;

    function getOpenRouterKey($key): ?string
    {
        $licenseKey = getLicenseKey();

        if (empty($licenseKey)) {
            return null;
        }

        $url = 'https://api.minniark.com/v1/data/openrouter/' . urlencode($licenseKey);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || $response === false) {
            return null;
        }

        $data = json_decode($response, true);
        return $data['openrouter_key'] ?? null;
    }

    function generateOpenRouterImageText()
    {
                
        $apiKey  = getenv('OPENROUTER_API_KEY');
        $model   = 'mistralai/mistral-small';
        $prompt  = 'Give me one short fun fact about PHP.';
        $referer = 'https://example.com'; // optional
        $title   = 'My PHP App';               // optional

        $text = Client::OpenRouterChat($apiKey, $model, $prompt, $referer, $title);

        return $text;
    }