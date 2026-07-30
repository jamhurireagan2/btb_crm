<?php
require_once 'config/database.php';

echo "<h1>🔑 Fix Login</h1>";

// Check if we have a database connection
try {
    $pdo->query("SELECT 1");
    echo "<p style='color:green;'>✅ Database connected successfully!</p>";
} catch(PDOException $e) {
    echo "<p style='color:red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Reset admin password
$username = 'admin';
$new_password = 'password123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Update existing admin
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$hashed_password, $username]);
        echo "<p style='color:green;'>✅ Admin password updated!</p>";
    } else {
        // Create new admin
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashed_password]);
        echo "<p style='color:green;'>✅ Admin user created!</p>";
    }
    
    echo "<br><strong>Login Credentials:</strong><br>";
    echo "Username: <strong>admin</strong><br>";
    echo "Password: <strong>password123</strong><br>";
    
} catch(PDOException $e) {
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<br><a href='index.php' style='font-size:18px;'>🔗 Go to Login</a>";
?>