<?php


function getOpenAIKey($key): ?string
{
    $licenseKey = getLicenseKey();

    if (empty($licenseKey)) {
        return null;
    }

    $url = 'https://api.minniark.com/v1/data/openai/' . urlencode($licenseKey);

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
    return $data['openai_key'] ?? null;
}



function generateOpenAIImageText(array $meta, int $targetWords = 250, string $language = 'en'): string
{
    // Modell fest auf günstiges GPT-4 setzen
    $MODEL = 'gpt-5-mini';

    // OpenAI-Key holen
    $apiKey = getOpenAIKey(null);
    if (empty($apiKey)) {
        return "Error: No OpenAI API key available.";
    }

    // Ziel-Wortzahl absichern
    $targetWords = max(50, $targetWords);

    // Tokens sparsam kalkulieren (~0.75 Worte je Token) + Sicherheitsaufschlag
    $approxTokens = (int)ceil($targetWords / 0.75) + 150; // Puffer für System/Userprompt
    $maxTokens = min(900, max(300, $approxTokens));       // harte Grenzen

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

    $messages = [
        [
            "role" => "system",
            "content" =>
                "You are a helpful assistant that writes vivid, natural-sounding photo descriptions for websites.
- Write in {$language}.
- Output ONLY the description paragraph, no titles, no markdown, no bullet points.
- Aim for about {$targetWords} words.
- Keep it accessible (avoid heavy jargon), include subtle photographic details when justified by metadata.
- Do not invent facts beyond what metadata reasonably implies."
        ],
        [
            "role" => "user",
            "content" =>
                "Write a single-paragraph description for a photo using the metadata below. ".
                "Do not include headings. If you reference location, be generic unless coordinates clearly identify a place.\n\n".
                "PHOTO METADATA:\n".$metadataBlock
        ]
    ];

    $payload = [
        "model"       => $MODEL,
        "messages"    => $messages,
        "temperature" => 0.7,
        "max_tokens"  => $maxTokens
    ];

    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Bearer {$apiKey}",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        CURLOPT_TIMEOUT        => 30
    ]);

    $response = curl_exec($ch);
    $errno    = curl_errno($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($errno) {
        return "Error: OpenAI request failed ({$errno}) {$error}";
    }

    $data = json_decode($response, true);
    if (!is_array($data)) {
        return "Error: Invalid OpenAI response.";
    }
    if (isset($data['error'])) {
        $msg = $data['error']['message'] ?? 'Unknown error';
        return "Error: {$msg}";
    }

    $text = $data['choices'][0]['message']['content'] ?? null;
    if (!$text) {
        return "Error: No content returned.";
    }

    $text = trim($text);
    $text = preg_replace('/^#+\s*/', '', $text);
    $text = trim($text, "\"' \t\n\r\0\x0B");

    return $text;
}

