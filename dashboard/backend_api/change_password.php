<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);

// File: change_password.php
session_start();


require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../functions/function_backend.php'; // für getUserDataFromUsername() & updateUserData()

security_checklogin();

use Symfony\Component\Yaml\Yaml;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Nur POST-Anfragen erlaubt.']);
    exit;
}

// Eingeloggt?
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Nicht angemeldet.']);
    exit;
}

$currentPassword = trim($_POST['current_password'] ?? '');
$newPassword     = trim($_POST['new_password'] ?? '');
$confirmPassword = trim($_POST['confirm_password'] ?? '');

$username = $_SESSION['username'];
$userData = getUserDataFromUsername($username);

error_log(print_r($userData, true));

if (!$userData || !isset($userData['password'])) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Something\'s wrong with the userdata.']);
    exit;
}

// Aktuelles Passwort prüfen
if (!password_verify($currentPassword, $userData['password'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Current password is wrong.']);
    exit;
}

// Neue Passwörter abgleichen
if ($newPassword !== $confirmPassword) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Neue Passwörter stimmen nicht überein.']);
    exit;
}

// Passwort hashen und speichern
$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

$update = [
    'password' => $newPassword
];

$success = updateUserData($username, $update, $username);

if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Password succesfully changed.']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Password can\'t save .']);
}
