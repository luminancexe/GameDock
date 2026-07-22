<?php
require_once "includes/connection.php";
session_start();

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET remember_token_hash = NULL, remember_token_expires = NULL WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', ['expires' => time() - 3600, 'path' => '/']);
}

session_unset();
session_destroy();
header("Location: login.php");
exit;
?>
