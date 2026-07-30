<?php
require_once 'config/database.php';

// Set new admin password
$username = 'admin';
$password = 'password123';  // Change this to whatever you want

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Delete existing admin
    $stmt = $pdo->prepare("DELETE FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    // Insert new admin
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashed_password]);
    
    echo "✅ Password reset successful!<br>";
    echo "Username: $username<br>";
    echo "Password: $password<br>";
    echo "<br><a href='index.php'>Go to Login</a>";
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>