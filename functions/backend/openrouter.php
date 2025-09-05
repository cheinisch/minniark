<?php

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