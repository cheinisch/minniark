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

    function generateOpenRouterImageText(array $meta, array $tags, int $targetWords = 250, string $language = 'en-us', string $url = '')
    {

        // Tags als einfache Liste darstellen
        $tagLines = [];

        if (!empty($tags) && is_array($tags)) {
            foreach ($tags as $tag) {
                // gegen leere Einträge absichern
                if (!empty($tag)) {
                    $tagLines[] = "- " . $tag;
                }
            }
        }

        // Block aus den Tags bauen
        $tagBlock = implode("\n", $tagLines);


        // Metadaten zu Textblock formen
        $lines = [];
        if (!empty($meta['title']))        $lines[] = "Title: " . $meta['title'];
        if (!empty($meta['camera']))       $lines[] = "Camera: " . $meta['camera'];
        if (!empty($meta['lens']))         $lines[] = "Lens: " . $meta['lens'];
        if (!empty($meta['aperture']))     $lines[] = "Aperture: " . $meta['aperture'];
        if (!empty($meta['shutter']))      $lines[] = "Shutter Speed: " . $meta['shutter'];
        if (!empty($meta['iso']))          $lines[] = "ISO: " . $meta['iso'];
        if (!empty($meta['focal_length'])) $lines[] = "Focal Length: " . $meta['focal_length'];
        if (!empty($meta['date_taken']))   $lines[] = "Date Taken: " . $meta['date_taken'];
        if (!empty($meta['gps']['has']) && !empty($meta['gps']['lat']) && !empty($meta['gps']['lon'])) {
            $lines[] = "GPS: " . $meta['gps']['lat'] . ", " . $meta['gps']['lon'];
        }
        $metadataBlock = implode("\n", $lines);
                
        $apiKey  = getOpenRouterKey(null);
        $model   = 'openai/gpt-4o-mini';
        $prompt = <<<PROMPT
        You are a helpful assistant that writes vivid, natural-sounding photo descriptions for websites.
        - Write in $language.
        - Output ONLY the description paragraph, no titles, no markdown, no bullet points.
        - Aim for about $targetWords words.
        - Keep it accessible (avoid heavy jargon), include subtle photographic details when justified by metadata.
        - Do not invent facts beyond what metadata reasonably implies.

        Write a single-paragraph description for a photo using the metadata below.
        Do not include headings. If you reference location, be generic unless coordinates clearly identify a place.

        PHOTO METADATA:
        $metadataBlock

        PHOTO TAGS:
        $tagBlock

        IMAGE URL: $url
        PROMPT;
        $referer = 'https://minniark.app'; // optional
        $title   = 'Minniark';               // optional

        $text = Client::OpenRouterChat($apiKey, $model, $prompt, $referer, $title);

        return $text;
    }