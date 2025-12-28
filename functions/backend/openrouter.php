<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';

use cheinisch\OpenRouterClient;
use Symfony\Component\Yaml\Yaml;

function _or_mask(?string $s, int $keepStart = 4, int $keepEnd = 2): string
{
    if (!is_string($s) || $s === '') return '';
    $len = strlen($s);
    if ($len <= ($keepStart + $keepEnd + 3)) return str_repeat('*', $len);
    return substr($s, 0, $keepStart) . str_repeat('*', max(3, $len - $keepStart - $keepEnd)) . substr($s, -$keepEnd);
}

function _or_log(string $tag, array $data = []): void
{
    error_log("[openrouter] {$tag} | " . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function getInstanceUUID(): string
{
    $uuid = getenv('MINNIARK_INSTANCE_UUID');
    if (is_string($uuid) && trim($uuid) !== '') {
        $u = trim($uuid);
        _or_log('uuid_env', ['uuid_masked' => _or_mask($u, 6, 4)]);
        return $u;
    }

    $settingsFile = __DIR__ . '/../../userdata/config/settings.yml';
    if (is_file($settingsFile)) {
        try {
            $yaml = Yaml::parseFile($settingsFile);
            $candidates = [
                $yaml['uuid'] ?? null,
                $yaml['instance_uuid'] ?? null,
                $yaml['license']['uuid'] ?? null,
            ];
            foreach ($candidates as $c) {
                if (is_string($c) && trim($c) !== '') {
                    $u = trim($c);
                    _or_log('uuid_settings', ['file' => $settingsFile, 'uuid_masked' => _or_mask($u, 6, 4)]);
                    return $u;
                }
            }
            _or_log('uuid_settings_missing', ['file' => $settingsFile]);
        } catch (Throwable $e) {
            _or_log('uuid_settings_parse_failed', ['file' => $settingsFile, 'err' => $e->getMessage()]);
        }
    } else {
        _or_log('uuid_settings_file_missing', ['file' => $settingsFile]);
    }

    return '';
}

function _curl_json(string $url, string $method = 'GET', ?array $jsonBody = null): array
{
    $method = strtoupper($method);
    $headers = ['Accept: application/json'];

    $ch = curl_init($url);

    if ($method === 'POST') {
        $headers[] = 'Content-Type: application/json; charset=utf-8';
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonBody ?? [], JSON_UNESCAPED_SLASHES));
    } else {
        curl_setopt($ch, CURLOPT_HTTPGET, true);
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 12,
        CURLOPT_HTTPHEADER     => $headers,
    ]);

    $raw     = curl_exec($ch);
    $http    = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    $rawStr  = is_string($raw) ? $raw : '';
    $preview = mb_substr($rawStr, 0, 600);

    if ($raw === false) {
        _or_log('curl_error', ['method' => $method, 'url' => $url, 'http' => $http, 'err' => $curlErr]);
        return ['ok' => false, 'http' => $http, 'json' => [], 'raw_preview' => $preview];
    }

    $json = json_decode($rawStr, true);
    if (!is_array($json)) {
        _or_log('bad_json', ['method' => $method, 'url' => $url, 'http' => $http, 'raw_preview' => $preview]);
        return ['ok' => false, 'http' => $http, 'json' => [], 'raw_preview' => $preview];
    }

    _or_log('proxy_response', [
        'method' => $method,
        'url'    => $url,
        'http'   => $http,
        'ok'     => $json['ok'] ?? null,
        'error'  => $json['error'] ?? null,
        'details_error' => $json['details']['error'] ?? null,
        'has_openrouter_key' => isset($json['openrouter_key']) && is_string($json['openrouter_key']) && $json['openrouter_key'] !== '',
    ]);

    return [
        'ok'          => ($http === 200) && (($json['ok'] ?? true) !== false),
        'http'        => $http,
        'json'        => $json,
        'raw_preview' => $preview,
    ];
}

function getOpenRouterKey(?string $licenseKey = null): ?string
{
    if (!function_exists('getLicenseKey')) {
        _or_log('missing_fn_getLicenseKey');
        return null;
    }

    if (!is_string($licenseKey) || trim($licenseKey) === '') {
        $licenseKey = getLicenseKey();
    }

    if (!is_string($licenseKey) || trim($licenseKey) === '') {
        _or_log('no_license_key');
        return null;
    }
    $licenseKey = trim($licenseKey);

    $uuid = getInstanceUUID();
    if ($uuid === '') {
        _or_log('no_instance_uuid', ['license_masked' => _or_mask($licenseKey)]);
        return null;
    }

    $base = getenv('MINNIARK_OPENROUTER_PROXY_URL');
    if (!is_string($base) || trim($base) === '') {
        $base = 'https://api.minniark.com/v1/data/openrouter';
    }
    $base = rtrim($base, '/');

    // ---------- TRY POST (Proxy-Kompatibel: openrouter_param + uuid) ----------
    $payload = [
        // Proxy alt/aktueller Name
        'openrouter_param' => $licenseKey,
        'uuid'             => $uuid,

        // zusätzlich (für neue Proxy-Versionen)
        'license_key'      => $licenseKey,
        'instance_uuid'    => $uuid,
    ];

    _or_log('proxy_request', [
        'url' => $base,
        'method' => 'POST',
        'license_masked' => _or_mask($licenseKey),
        'uuid_masked' => _or_mask($uuid, 6, 4),
        'payload_keys' => array_keys($payload),
    ]);

    $resp = _curl_json($base, 'POST', $payload);

    $key = $resp['json']['openrouter_key'] ?? null;
    if ($resp['ok'] && is_string($key) && trim($key) !== '') {
        _or_log('proxy_key_ok', ['http' => $resp['http'], 'key_masked' => _or_mask(trim($key), 6, 4)]);
        return trim($key);
    }

    // ---------- Fallback GET (falls Proxy nur Query akzeptiert) ----------
    $getUrl = $base
        . '?openrouter_param=' . rawurlencode($licenseKey)
        . '&uuid=' . rawurlencode($uuid)
        . '&license_key=' . rawurlencode($licenseKey)
        . '&instance_uuid=' . rawurlencode($uuid);

    error_log("URL: ". $getUrl);

    _or_log('proxy_request_fallback', ['method' => 'GET', 'url' => $getUrl]);

    $resp2 = _curl_json($getUrl, 'GET', null);

    $key2 = $resp2['json']['openrouter_key'] ?? null;
    if ($resp2['ok'] && is_string($key2) && trim($key2) !== '') {
        _or_log('proxy_key_ok_get', ['http' => $resp2['http'], 'key_masked' => _or_mask(trim($key2), 6, 4)]);
        return trim($key2);
    }

    _or_log('proxy_key_fail', [
        'post_http' => $resp['http'],
        'post_error' => $resp['json']['error'] ?? null,
        'post_details' => $resp['json']['details'] ?? null,
        'get_http' => $resp2['http'],
        'get_error' => $resp2['json']['error'] ?? null,
        'get_details' => $resp2['json']['details'] ?? null,
        'post_raw_preview' => $resp['raw_preview'],
        'get_raw_preview' => $resp2['raw_preview'],
    ]);

    return null;
}

function generateOpenRouterImageText(array $meta, array $tags, int $targetWords = 250, string $language = 'en-us', string $url = ''): string
{
    $tagLines = [];
    if (!empty($tags) && is_array($tags)) {
        foreach ($tags as $tag) {
            if (is_string($tag)) {
                $t = trim($tag);
                if ($t !== '') $tagLines[] = "- " . $t;
            }
        }
    }
    $tagBlock = implode("\n", $tagLines);

    $lines = [];
    if (!empty($meta['title']))        $lines[] = "Title: " . (string)$meta['title'];
    if (!empty($meta['camera']))       $lines[] = "Camera: " . (string)$meta['camera'];
    if (!empty($meta['lens']))         $lines[] = "Lens: " . (string)$meta['lens'];
    if (!empty($meta['aperture']))     $lines[] = "Aperture: " . (string)$meta['aperture'];
    if (!empty($meta['shutter']))      $lines[] = "Shutter Speed: " . (string)$meta['shutter'];
    if (!empty($meta['iso']))          $lines[] = "ISO: " . (string)$meta['iso'];
    if (!empty($meta['focal_length'])) $lines[] = "Focal Length: " . (string)$meta['focal_length'];
    if (!empty($meta['date_taken']))   $lines[] = "Date Taken: " . (string)$meta['date_taken'];

    $gpsHas = !empty($meta['gps']['has']);
    $gpsLat = $meta['gps']['lat'] ?? null;
    $gpsLon = $meta['gps']['lon'] ?? null;
    if ($gpsHas && $gpsLat !== null && $gpsLon !== null && (string)$gpsLat !== '' && (string)$gpsLon !== '') {
        $lines[] = "GPS: " . $gpsLat . ", " . $gpsLon;
    }

    $metadataBlock = implode("\n", $lines);

    $apiKey = getOpenRouterKey(null);
    if (!is_string($apiKey) || trim($apiKey) === '') {
        _or_log('abort_no_api_key', []);
        return 'Error: OpenRouter API key not available (license invalid / proxy error).';
    }

    $model  = 'openai/gpt-4o-mini';

    $prompt = <<<PROMPT
You are a helpful assistant that writes vivid, natural-sounding photo descriptions for websites.
- Write in {$language}.
- Output ONLY the description paragraph, no titles, no markdown, no bullet points.
- Aim for about {$targetWords} words.
- Keep it accessible (avoid heavy jargon), include subtle photographic details when justified by metadata.
- Do not invent facts beyond what metadata reasonably implies.

Write a single-paragraph description for a photo using the metadata below.
Do not include headings. If you reference location, be generic unless coordinates clearly identify a place.

PHOTO METADATA:
{$metadataBlock}

PHOTO TAGS:
{$tagBlock}

IMAGE URL: {$url}
PROMPT;

    $referer = 'https://minniark.app';
    $title   = 'Minniark';

    _or_log('openrouter_chat_call', [
        'model' => $model,
        'language' => $language,
        'targetWords' => $targetWords,
        'apiKey_masked' => _or_mask($apiKey, 6, 4),
    ]);

    try {
        $text = OpenRouterClient::OpenRouterChat($apiKey, $model, $prompt, $referer, $title);
    } catch (Throwable $e) {
        _or_log('openrouter_chat_error', ['err' => $e->getMessage()]);
        return 'Error: OpenRouter request failed.';
    }

    if (!is_string($text) || trim($text) === '') {
        _or_log('openrouter_empty_response', []);
        return 'Error: OpenRouter returned empty response.';
    }

    return trim($text);
}
