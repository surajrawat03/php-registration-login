<?php
session_start();
require_once '../../vendor/autoload.php';

use App\class\auth;

$authController = new auth();
$result = $authController->logout();

header('Location: ../index.php?message=' . urlencode($result['message']) . '&type=success');
exit();
?>
