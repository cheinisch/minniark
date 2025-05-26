<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once(__DIR__ . "/../../functions/function_backend.php");
security_checklogin();

$slug = $_POST['slug'] ?? '';
$newName = trim($_POST['slug'] ?? '');
$newImage = trim($_POST['image'] ?? '');

$data = [
    'image' => trim($_POST['image']),
    // Wenn du keine Alben aktualisieren willst, lasse 'albums' weg
];

$result = updateCollection($slug, $data, $slug);

    if($result)
    {
        header("Location: ../collection-detail.php?collection=$slug");
    }
exit;

?>
