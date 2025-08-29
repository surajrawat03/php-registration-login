<?php
session_start();
require_once '../../vendor/autoload.php';

use App\class\auth;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    $authController = new auth();
    $result = $authController->login($email, $password, $remember);

    if ($result['success']) {
        header('Location: ../dashboard.php');
        exit();
    } else {
        header('Location: ../index.php?message=' . urlencode($result['message']) . '&type=danger');
        exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}
?>
