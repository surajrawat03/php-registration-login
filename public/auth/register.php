<?php
session_start();
require_once '../../vendor/autoload.php';

use App\class\auth;

if ($_POST) {
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Basic validation
    if ($password !== $confirmPassword) {
        header('Location: ../index.php?message=' . urlencode('Passwords do not match!') . '&type=danger');
        exit();
    }

    if (strlen($password) < 6) {
        header('Location: ../index.php?message=' . urlencode('Password must be at least 6 characters long!') . '&type=danger');
        exit();
    }

    $authController = new auth();
    $result = $authController->register($firstName, $lastName, $email, $password);

    if ($result['success']) {
        header('Location: ../index.php?message=' . urlencode($result['message']) . '&type=success');
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
