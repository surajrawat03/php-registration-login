<?php
session_start();
require_once '../vendor/autoload.php';

use App\class\auth;

$authController = new auth();

if (!$authController->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$user = $authController->getCurrentUser();

$pageTitle = 'Dashboard - PHP Registration & Login System';
$extraStyles = '<style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .dashboard-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
    </style>';
include __DIR__ . '/layout/top.php';
?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="dashboard-container p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0">Welcome, <?php echo htmlspecialchars($user['firstName']); ?>!</h1>
                        <a href="auth/logout.php" class="btn btn-outline-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-person-circle" style="font-size: 3rem; color: #667eea;"></i>
                                    <h5 class="card-title mt-3">Profile Information</h5>
                                    <div class="text-start">
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                        <p><strong>User ID:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-shield-check" style="font-size: 3rem; color: #667eea;"></i>
                                    <h5 class="card-title mt-3">Account Status</h5>
                                    <p class="card-text">Your account is active and secure.</p>
                                    <div class="alert alert-success">
                                        <i class="bi bi-check-circle me-2"></i>Successfully logged in
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-gear me-2"></i>Account Actions
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <button class="btn btn-outline-primary w-100">
                                                <i class="bi bi-pencil me-2"></i>Edit Profile
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-outline-warning w-100">
                                                <i class="bi bi-key me-2"></i>Change Password
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-outline-info w-100">
                                                <i class="bi bi-bell me-2"></i>Notifications
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include __DIR__ . '/layout/bottom.php'; ?>
