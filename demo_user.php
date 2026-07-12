<?php
require_once "includes/connection.php";
$pass = password_hash('password123', PASSWORD_DEFAULT);
$pdo->exec("INSERT IGNORE INTO users (fullname, email, phone, password, created_at) VALUES ('Demo User', 'demo@gamehub.com', '1234567890', '$pass', NOW())");
echo 'Demo user created.';
?>
