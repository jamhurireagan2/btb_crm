<?php
$host = 'sql301.infinityfree.com';
$dbname = 'if0_42536062_btb_crm';
$username = 'if0_42536062';
$password = 'gFLvCmEVGi2z';  // Try your InfinityFree account password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
session_start();
?>