<?php
require_once 'config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BTB Insurance - Client Management</title>
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-wrapper">
        <div class="login-container">
            <!-- Brand -->
            <div class="login-brand">
                <div class="brand-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1>BTB Insurance Brokers</h1>
                <p class="brand-subtitle">Client Management System</p>
            </div>

            <!-- Error Message -->
            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label>
                        <i class="fas fa-user"></i>
                        Username
                    </label>
                    <input type="text" name="username" placeholder="Enter your username" required>
                </div>
                
                <div class="form-group">
                    <label>
                        <i class="fas fa-lock"></i>
                        Password
                    </label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>

            <div class="login-footer">
                <p>Default: <strong>admin</strong> / <strong>password123</strong></p>
                <p class="copyright">&copy; <?= date('Y') ?> BTB Insurance Brokers Ltd. All rights reserved.</p>
            </div>
        </div>

        <!-- Decorative Side -->
        <div class="login-hero">
            <div class="hero-content">
                <i class="fas fa-building"></i>
                <h2>Welcome Back</h2>
                <p>Manage your clients efficiently with our digital platform.</p>
                <div class="hero-features">
                    <span><i class="fas fa-check-circle"></i> Client Management</span>
                    <span><i class="fas fa-check-circle"></i> Policy Tracking</span>
                    <span><i class="fas fa-check-circle"></i> Renewal Alerts</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>