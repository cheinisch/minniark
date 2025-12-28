<?php
declare(strict_types=1);

require_once __DIR__ . "/../../functions/function_backend.php";
require_once __DIR__ . "/../../app/autoload.php";

// WICHTIG: keine session_start() hier – security_checklogin() macht das in deinem System bereits
security_checklogin();

header('Content-Type: application/json; charset=utf-8');

$debugMode = isset($_GET['debug']) && $_GET['debug'] === '1';

function jexit(int $code, array $payload): void {
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function log_img(string $tag, array $data = []): void {
    $safe = $data;

    // Logs begrenzen (Description kann lang sein)
    if (isset($safe['raw_preview']) && is_string($safe['raw_preview']) && strlen($safe['raw_preview']) > 800) {
        $safe['raw_preview'] = substr($safe['raw_preview'], 0, 800) . '...';
    }
    if (isset($safe['description_preview']) && is_string($safe['description_preview']) && strlen($safe['description_preview']) > 300) {
        $safe['description_preview'] = substr($safe['description_preview'], 0, 300) . '...';
    }

    error_log('[image_save] ' . $tag . ' | ' . json_encode($safe, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

// ---------- Method ----------
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    jexit(405, ['ok' => false, 'error' => 'Method Not Allowed']);
}

// ---------- Read JSON ----------
$raw = file_get_contents('php://input') ?: '';
log_img('incoming_raw', [
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? '',
    'content_len'  => (string)strlen($raw),
    'raw_preview'  => $raw,
]);

$data = json_decode($raw, true);

if (!is_array($data)) {
    log_img('invalid_json', ['json_error' => json_last_error_msg()]);
    jexit(400, ['ok' => false, 'error' => 'Invalid JSON']);
}

log_img('incoming_json', [
    'keys' => array_keys($data),
]);

// ---------- Input ----------
$file = isset($data['file']) ? basename((string)$data['file']) : '';
if ($file === '') {
    log_img('missing_file', ['received_keys' => array_keys($data)]);
    jexit(400, ['ok' => false, 'error' => 'Missing file']);
}

// erlaubte Felder: title (optional), description (optional aber sinnvoll)
$title = isset($data['title']) ? (string)$data['title'] : '';
$desc  = isset($data['description']) ? (string)$data['description'] : '';

$title = trim($title);
// description: NICHT trimmen, sonst entfernst du absichtlich gewollte führende Leerzeilen.
// Aber: normalisieren von CRLF -> LF ist okay.
$desc = str_replace(["\r\n", "\r"], "\n", $desc);

// optional: leere Strings zulassen (dann wird die md geleert)
log_img('input_ok', [
    'file' => $file,
    'title_len' => (string)strlen($title),
    'description_len' => (string)strlen($desc),
    'description_preview' => $desc,
]);

// ---------- YAML/MD path guess (für Log) ----------
$slug = pathinfo($file, PATHINFO_FILENAME);
$projectRoot = realpath(__DIR__ . '/../../') ?: (__DIR__ . '/../../');
$yamlGuess = $projectRoot . '/userdata/content/images/' . $slug . '.yml';
$mdGuess   = $projectRoot . '/userdata/content/images/' . $slug . '.md';

log_img('paths_guess', [
    'project_root' => $projectRoot,
    'yaml_path' => $yamlGuess,
    'yaml_exists' => file_exists($yamlGuess),
    'md_path' => $mdGuess,
    'md_exists' => file_exists($mdGuess),
]);

// ---------- Load image ----------
$imageData = getImage($file);
if (!is_array($imageData) || empty($imageData['filename'])) {
    log_img('image_not_found', ['file' => $file]);
    jexit(404, ['ok' => false, 'error' => 'Image not found']);
}

// ---------- Save via updateImage(type=description) ----------
try {
    $saveData = [
        'filename'    => $imageData['filename'], // MUSS gesetzt sein
        'title'       => $title,                 // optional
        'description' => $desc,                  // wird in .md geschrieben
    ];

    log_img('updateImage_call', [
        'type' => 'description',
        'saveData' => [
            'filename' => $saveData['filename'],
            'has_title' => ($title !== ''),
            'desc_len' => (string)strlen($desc),
        ],
    ]);

    $ok = updateImage($saveData, 'description');

    if (!$ok) {
        log_img('updateImage_failed', ['file' => $file]);
        jexit(500, ['ok' => false, 'error' => 'updateImage failed']);
    }

    // neu laden, damit wir den gespeicherten Stand zurückgeben
    $fresh = getImage($file);

    $resp = [
        'ok' => true,
        'file' => $file,
        'title' => $fresh['title'] ?? '',
        'description' => $fresh['description'] ?? '',
    ];

    if ($debugMode) {
        $resp['debug'] = [
            'received_keys' => array_keys($data),
            'yaml_path_guess' => $yamlGuess,
            'md_path_guess' => $mdGuess,
            'saveData' => [
                'filename' => $saveData['filename'],
                'title' => $saveData['title'],
                'description_len' => strlen($saveData['description']),
            ],
        ];
    }

    jexit(200, $resp);

} catch (Throwable $e) {
    log_img('exception', ['msg' => $e->getMessage()]);
    jexit(500, ['ok' => false, 'error' => $e->getMessage()]);
}
