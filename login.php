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
    <title>BTB Insurance - Login</title>
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-page">
    <!-- Animated Background -->
    <div class="login-bg">
        <div class="bg-shape shape-1"></div>
        <div class="bg-shape shape-2"></div>
        <div class="bg-shape shape-3"></div>
    </div>

    <div class="login-wrapper">
        <!-- Left Side - Brand -->
        <div class="login-brand-section">
            <div class="brand-content">
                <div class="brand-logo">
                    <div class="logo-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h1>BTB Insurance</h1>
                        <p>Brokers Ltd</p>
                    </div>
                </div>

                <div class="brand-tagline">
                    <h2>Welcome Back</h2>
                    <p>Manage your client portfolio with ease and efficiency.</p>
                </div>

                <div class="brand-features">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-users"></i></div>
                        <div>
                            <h4>Client Management</h4>
                            <p>Track all your clients in one place</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-file-contract"></i></div>
                        <div>
                            <h4>Policy Tracking</h4>
                            <p>Monitor policies and renewals</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-bell"></i></div>
                        <div>
                            <h4>Smart Alerts</h4>
                            <p>Never miss a renewal again</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-form-section">
            <div class="form-container">
                <div class="form-header">
                    <h3>Sign In</h3>
                    <p>Enter your credentials to access your dashboard</p>
                </div>

                <?php if($error): ?>
                    <div class="alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

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

                <div class="form-footer">
                    <p>Demo: <strong>admin</strong> / <strong>password123</strong></p>
                    <p class="copyright">&copy; <?= date('Y') ?> BTB Insurance Brokers Ltd.</p>
                </div>
            </div>
        </div>
    </div>

    <button class="theme-toggle-float" onclick="toggleTheme()" title="Toggle Theme">
        <i class="fas fa-moon"></i>
    </button>

    <script>
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            const icon = document.querySelector('.theme-toggle-float i');
            if (document.body.classList.contains('dark-mode')) {
                icon.className = 'fas fa-sun';
            } else {
                icon.className = 'fas fa-moon';
            }
        }
    </script>
</body>
</html>