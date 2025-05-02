<?php
header('Content-Type: application/json');

$baseDir = __DIR__ . '/../../userdata/content/essays/';
$baseSlug = isset($_GET['base']) ? preg_replace('/[^a-z0-9\-]/', '', strtolower($_GET['base'])) : '';

if (!$baseSlug) {
    echo json_encode(['error' => 'Invalid base name']);
    exit;
}

$suggested = $baseSlug;
$counter = 1;

while (is_dir($baseDir . $suggested)) {
    $suggested = $baseSlug . '_' . $counter;
    $counter++;
}

echo json_encode(['suggested' => $suggested]);
