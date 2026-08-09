<?php
/**
 * upload_theme.php
 * Accepts a theme ZIP upload, validates structure, extracts to userdata/template/.
 * Returns JSON: { success: bool, message: string, folder?: string, name?: string }
 *
 * Security hardening:
 *  - Admin-only check
 *  - MIME + extension validation
 *  - File size cap (MAX_ZIP_BYTES)
 *  - ZIP entry count cap (MAX_ZIP_ENTRIES)
 *  - Zip-Slip protection (realpath containment check)
 *  - No path traversal in folder name
 */

require_once(__DIR__ . '/../../functions/function_backend.php');
security_checklogin();
onlyAdmin();

header('Content-Type: application/json');

// ── Constants ───────────────────────────────────────────────────────────────

const MAX_ZIP_BYTES   = 20 * 1024 * 1024; // 20 MB
const MAX_ZIP_ENTRIES = 500;

// ── Helpers ─────────────────────────────────────────────────────────────────

function jsonError(string $msg, int $code = 400): never
{
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $msg]);
    exit;
}

function jsonOk(array $payload): never
{
    echo json_encode(array_merge(['success' => true], $payload));
    exit;
}

// ── Gate checks ─────────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed.', 405);
}

if (!isset($_FILES['theme_zip']) || $_FILES['theme_zip']['error'] !== UPLOAD_ERR_OK) {
    $err = $_FILES['theme_zip']['error'] ?? -1;
    $messages = [
        UPLOAD_ERR_INI_SIZE   => 'File exceeds upload_max_filesize.',
        UPLOAD_ERR_FORM_SIZE  => 'File exceeds MAX_FILE_SIZE.',
        UPLOAD_ERR_PARTIAL    => 'Upload was only partial.',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION  => 'Upload stopped by extension.',
    ];
    jsonError($messages[$err] ?? 'Upload failed (error ' . $err . ').');
}

$uploaded = $_FILES['theme_zip'];

// ── File size check (before opening) ────────────────────────────────────────

if ($uploaded['size'] > MAX_ZIP_BYTES) {
    jsonError('ZIP file too large. Maximum allowed size is ' . (MAX_ZIP_BYTES / 1024 / 1024) . ' MB.');
}

// ── MIME / extension check ───────────────────────────────────────────────────

$ext = strtolower(pathinfo($uploaded['name'], PATHINFO_EXTENSION));
if ($ext !== 'zip') {
    jsonError('Only .zip files are accepted.');
}

$finfo    = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $uploaded['tmp_name']);
finfo_close($finfo);

$allowedMimes = ['application/zip', 'application/x-zip-compressed', 'application/octet-stream'];
if (!in_array($mimeType, $allowedMimes, true)) {
    jsonError('Invalid file type: ' . htmlspecialchars($mimeType));
}

// ── Open ZIP ─────────────────────────────────────────────────────────────────

$zip = new ZipArchive();
$res = $zip->open($uploaded['tmp_name']);
if ($res !== true) {
    jsonError('Could not open ZIP archive (code ' . $res . ').');
}

// ── Entry count check (Zip-Bomb-Light) ───────────────────────────────────────

if ($zip->numFiles > MAX_ZIP_ENTRIES) {
    $zip->close();
    jsonError('ZIP contains too many files (max ' . MAX_ZIP_ENTRIES . ').');
}

// ── Validate ZIP contents: look for theme.json + image.png ───────────────────

$themeJsonPath = null;
$imagePngPath  = null;

for ($i = 0; $i < $zip->numFiles; $i++) {
    $name = $zip->getNameIndex($i);
    if (str_ends_with($name, 'theme.json')) { $themeJsonPath = $name; }
    if (str_ends_with($name, 'image.png'))  { $imagePngPath  = $name; }
}

if ($themeJsonPath === null) {
    $zip->close();
    jsonError('Invalid theme: theme.json not found inside the ZIP.');
}
if ($imagePngPath === null) {
    $zip->close();
    jsonError('Invalid theme: image.png (preview) not found inside the ZIP.');
}

// ── Read & validate theme.json ────────────────────────────────────────────────

$themeJsonContent = $zip->getFromName($themeJsonPath);
if ($themeJsonContent === false) {
    $zip->close();
    jsonError('Could not read theme.json from ZIP.');
}

$themeData = json_decode($themeJsonContent, true);
if (json_last_error() !== JSON_ERROR_NONE || !isset($themeData['name'])) {
    $zip->close();
    jsonError('theme.json is malformed or missing required "name" field.');
}

$themeName    = trim($themeData['name']);
$themeVersion = $themeData['version'] ?? '1.0';
$themeAuthor  = $themeData['author']  ?? 'Unknown';

if ($themeName === '') {
    $zip->close();
    jsonError('theme.json "name" field must not be empty.');
}

// ── Determine target folder name ──────────────────────────────────────────────

$folderName  = preg_replace('/[^a-z0-9_\-]/', '_', strtolower($themeName));
$templateDir = realpath(__DIR__ . '/../../userdata/template');

if (!$templateDir || !is_dir($templateDir)) {
    $zip->close();
    jsonError('Template directory not found on server.', 500);
}

$targetPath = $templateDir . DIRECTORY_SEPARATOR . $folderName;

// Prevent overwrite of existing theme
if (is_dir($targetPath)) {
    $zip->close();
    jsonError('A theme with the folder name "' . htmlspecialchars($folderName) . '" is already installed.');
}

// ── Extract to temp dir ───────────────────────────────────────────────────────

$tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'minniark_upload_' . uniqid('', true);
if (!@mkdir($tempDir, 0777, true)) {
    $zip->close();
    jsonError('Could not create temporary extraction directory.', 500);
}

// ── Zip-Slip protection: validate all entry paths before extracting ───────────

$realTempDir = realpath($tempDir);
for ($i = 0; $i < $zip->numFiles; $i++) {
    $entryName = $zip->getNameIndex($i);

    // Reject absolute paths or suspicious components
    if (
        str_starts_with($entryName, '/') ||
        str_contains($entryName, '..') ||
        str_contains($entryName, "\0")
    ) {
        $zip->close();
        deleteDir($tempDir);
        jsonError('ZIP contains invalid path: "' . htmlspecialchars($entryName) . '".');
    }

    // Check resolved path is inside tempDir
    $candidate = $realTempDir . DIRECTORY_SEPARATOR . $entryName;
    // Use str_starts_with on realpath to prevent traversal
    // We can't call realpath before extraction, so we normalise manually
    $normalised = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $candidate);
    if (!str_starts_with($normalised, $realTempDir . DIRECTORY_SEPARATOR)) {
        $zip->close();
        deleteDir($tempDir);
        jsonError('ZIP contains a path traversal entry.');
    }
}

if (!$zip->extractTo($tempDir)) {
    $zip->close();
    deleteDir($tempDir);
    jsonError('Failed to extract ZIP archive.', 500);
}
$zip->close();

// ── Locate extracted theme root ───────────────────────────────────────────────

$sourcePath = $tempDir;
$subdirs    = array_filter(glob($tempDir . DIRECTORY_SEPARATOR . '*'), 'is_dir');
if (count($subdirs) === 1) {
    $sourcePath = reset($subdirs);
}

if (!file_exists($sourcePath . DIRECTORY_SEPARATOR . 'theme.json')) {
    deleteDir($tempDir);
    jsonError('Extracted structure invalid: theme.json missing at root.', 500);
}

// ── Copy to final location ────────────────────────────────────────────────────

if (!@mkdir($targetPath, 0777, true)) {
    deleteDir($tempDir);
    jsonError('Could not create theme directory.', 500);
}

if (!copyDir($sourcePath, $targetPath)) {
    deleteDir($targetPath);
    deleteDir($tempDir);
    jsonError('Failed to copy theme files.', 500);
}

deleteDir($tempDir);

// ── Non-JS fallback redirect ──────────────────────────────────────────────────
// If the request was made without XHR (direct browser navigation), redirect back.
// XHR requests send X-Requested-With header via JS.
if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Location: ../dashboard-theme.php?uploaded=' . urlencode($themeName));
    exit;
}

// ── JSON success response ─────────────────────────────────────────────────────

jsonOk([
    'message' => 'Theme "' . htmlspecialchars($themeName) . '" installed successfully.',
    'folder'  => $folderName,
    'name'    => $themeName,
    'version' => $themeVersion,
    'author'  => $themeAuthor,
]);
