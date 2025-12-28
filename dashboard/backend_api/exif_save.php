<?php
declare(strict_types=1);

require_once __DIR__ . "/../../functions/function_backend.php";
require_once __DIR__ . "/../../app/autoload.php";

// WICHTIG: NICHT session_start() hier, weil security_checklogin() vermutlich selbst startet
security_checklogin();

header('Content-Type: application/json; charset=utf-8');

$debugMode = isset($_GET['debug']) && $_GET['debug'] === '1';

function jexit(int $code, array $payload): void {
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function log_exif(string $tag, array $data = []): void {
    // nie riesige Daten loggen -> begrenzen
    $safe = $data;
    if (isset($safe['raw_preview']) && is_string($safe['raw_preview']) && strlen($safe['raw_preview']) > 800) {
        $safe['raw_preview'] = substr($safe['raw_preview'], 0, 800) . '...';
    }
    error_log('[exif_save] ' . $tag . ' | ' . json_encode($safe, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

// ---------- Method ----------
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    jexit(405, ['ok' => false, 'error' => 'Method Not Allowed']);
}

// ---------- Read JSON ----------
$raw = file_get_contents('php://input') ?: '';
log_exif('incoming_raw', [
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? '',
    'content_len'  => (string)strlen($raw),
    'raw_preview'  => $raw,
]);

$data = json_decode($raw, true);

if (!is_array($data)) {
    log_exif('invalid_json', ['json_error' => json_last_error_msg()]);
    jexit(400, ['ok' => false, 'error' => 'Invalid JSON']);
}

log_exif('incoming_json', [
    'keys' => array_keys($data),
]);

// ---------- Input ----------
$file = isset($data['file']) ? basename((string)$data['file']) : '';
if ($file === '') {
    log_exif('missing_file', ['data_keys' => array_keys($data)]);
    jexit(400, ['ok' => false, 'error' => 'Missing file']);
}

// Unterstütze beide Payloads:
// A) flat: { file, camera, lens, ... lat, lon }
// B) nested: { file, exif: { camera:.. }, gps: { latitude:.. } }
$incomingExif = null;

if (isset($data['exif']) && is_array($data['exif'])) {
    $incomingExif = $data['exif'];
    log_exif('payload_mode', ['mode' => 'nested_exif', 'exif_keys' => array_keys($incomingExif)]);
} else {
    // flat keys sammeln
    $incomingExif = [];
    $flatKeys = ['camera','lens','aperture','shutter_speed','iso','focal_length','date','lat','lon'];
    foreach ($flatKeys as $k) {
        if (array_key_exists($k, $data)) {
            $incomingExif[$k] = $data[$k];
        }
    }
    log_exif('payload_mode', ['mode' => 'flat_exif', 'picked_keys' => array_keys($incomingExif)]);
}

// hier ist "exif" zwar vorhanden, aber kann leer sein
if (!is_array($incomingExif)) {
    log_exif('missing_exif', ['exif_type' => gettype($incomingExif)]);
    jexit(400, ['ok' => false, 'error' => 'Missing exif data']);
}

// ---------- YAML path guess ----------
$slug = pathinfo($file, PATHINFO_FILENAME);
$projectRoot = realpath(__DIR__ . '/../../') ?: (__DIR__ . '/../../');
$yamlGuess = $projectRoot . '/userdata/content/images/' . $slug . '.yml';
log_exif('yaml_path_guess', [
    'project_root' => $projectRoot,
    'yaml_path'    => $yamlGuess,
    'exists'       => file_exists($yamlGuess),
]);

// ---------- Load image ----------
$imageData = getImage($file);
if (!is_array($imageData) || empty($imageData['filename'])) {
    log_exif('image_not_found', ['file' => $file]);
    jexit(404, ['ok' => false, 'error' => 'Image not found']);
}

// ---------- Normalize (IMPORTANT: GPS must NOT be inside exif!) ----------
$map = [
    'camera'        => 'Camera',
    'lens'          => 'Lens',
    'aperture'      => 'Aperture',
    'shutter_speed' => 'Shutter Speed',
    'iso'           => 'ISO',
    'focal_length'  => 'Focal Length',
    'date'          => 'Date',
];

// Strings-only EXIF payload for updateImage()
$normalizedExif = [];

foreach ($map as $inKey => $yamlKey) {
    if (array_key_exists($inKey, $incomingExif)) {
        $val = $incomingExif[$inKey];

        // updateImage() trimmt -> hier sicherstellen: string
        if (is_array($val) || is_object($val)) {
            // ignorieren statt crashen
            log_exif('skip_non_scalar', ['key' => $inKey, 'type' => gettype($val)]);
            continue;
        }

        $val = trim((string)$val);
        if ($val !== '') {
            $normalizedExif[$yamlKey] = $val;
        }
    }
}

// GPS separat (so wie updateImage() es erwartet)
$gps = null;

// 1) flat lat/lon in incomingExif
$latRaw = $incomingExif['lat'] ?? null;
$lonRaw = $incomingExif['lon'] ?? null;

// 2) optional nested gps: { gps: { latitude, longitude } }
if (($latRaw === null || $lonRaw === null) && isset($data['gps']) && is_array($data['gps'])) {
    $latRaw = $latRaw ?? ($data['gps']['latitude'] ?? null);
    $lonRaw = $lonRaw ?? ($data['gps']['longitude'] ?? null);
}

$lat = ($latRaw !== null && $latRaw !== '') ? (float)$latRaw : null;
$lon = ($lonRaw !== null && $lonRaw !== '') ? (float)$lonRaw : null;

if ($lat !== null && $lon !== null) {
    $gps = [
        'latitude'  => $lat,
        'longitude' => $lon,
    ];
}

log_exif('normalized_payload', [
    'file' => $file,
    'exif_keys' => array_keys($normalizedExif),
    'gps' => $gps,
]);

// ---------- Save via updateImage (IMPORTANT: gps separate) ----------
try {
    $saveData = [
        'filename' => $imageData['filename'], // muss gesetzt sein
        'exif'     => $normalizedExif,
    ];
    if ($gps !== null) {
        $saveData['gps'] = $gps;
    }

    log_exif('updateImage_call', [
        'type' => 'exif',
        'saveData' => [
            'filename' => $saveData['filename'],
            'exif_keys' => array_keys($saveData['exif'] ?? []),
            'has_gps' => isset($saveData['gps']),
        ],
    ]);

    $ok = updateImage($saveData, 'exif');

    if (!$ok) {
        log_exif('updateImage_failed', ['file' => $file]);
        jexit(500, ['ok' => false, 'error' => 'updateImage failed']);
    }

    // Reload to return what is now stored
    $fresh = getImage($file);

    $resp = [
        'ok'   => true,
        'file' => $file,
        'exif' => $fresh['exif'] ?? [],
    ];

    if ($debugMode) {
        $resp['debug'] = [
            'received_keys' => array_keys($data),
            'yaml_path_guess' => $yamlGuess,
            'saveData' => $saveData,
        ];
    }

    jexit(200, $resp);

} catch (Throwable $e) {
    log_exif('exception', ['msg' => $e->getMessage()]);
    jexit(500, [
        'ok' => false,
        'error' => $e->getMessage(),
    ]);
}
