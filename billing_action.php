<?php
require_once "includes/connection.php";
require_once "includes/auth_check.php";
requireLogin();

if (($_GET['action'] ?? '') === 'remove') {
    $stmt = $pdo->prepare("UPDATE users SET billing_card_name = NULL, billing_card_last4 = NULL, billing_card_brand = NULL, billing_card_expiry = NULL WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}

header("Location: profile.php");
exit;
