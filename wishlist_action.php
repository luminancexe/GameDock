<?php
require_once "includes/connection.php";
require_once "includes/auth_check.php";
requireLogin();

if (isset($_GET['game_id'])) {
    $game_id = (int)$_GET['game_id'];
    $user_id = $_SESSION['user_id'];
    
    // Check if already in wishlist
    $stmt = $pdo->prepare("SELECT * FROM wishlists WHERE user_id = ? AND game_id = ?");
    $stmt->execute([$user_id, $game_id]);
    
    if ($stmt->fetch()) {
        // Remove it
        $stmt = $pdo->prepare("DELETE FROM wishlists WHERE user_id = ? AND game_id = ?");
        $stmt->execute([$user_id, $game_id]);
    } else {
        // Add it
        $stmt = $pdo->prepare("INSERT INTO wishlists (user_id, game_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $game_id]);
    }
}
$redirect = $_SERVER['HTTP_REFERER'] ?? 'wishlist.php';
header("Location: $redirect");
exit;
?>
