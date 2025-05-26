<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once(__DIR__ . "/../../functions/function_backend.php");
security_checklogin();

$albumName = $_POST['collection-current-title'] ?? '';
$newName = trim($_POST['collection-title-edit'] ?? '');
$newDesc = trim($_POST['collection-description'] ?? '');

$slug = generateSlug($newName);
$newSlug = generateSlug($_POST['collection-title-edit']);
$oldSlug = generateSlug($_POST['collection-current-title']);

$data = [
    'name' => trim($_POST['collection-title-edit']),
    'description' => trim($_POST['collection-description']),
    // Wenn du keine Alben aktualisieren willst, lasse 'albums' weg
];

$result = updateCollection($newSlug, $data, $oldSlug);

    if($result)
    {
        header("Location: ../collection-detail.php?collection=$slug");
    }
exit;

?>
