<?php
require_once "includes/connection.php";
require_once "includes/auth_check.php";
requireLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$game_id = filter_var($_POST['game_id'] ?? $_GET['game_id'] ?? null, FILTER_VALIDATE_INT);
$rental_days = filter_var($_POST['rental_days'] ?? null, FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];

if ($action === 'add' && $game_id) {
    // 0 stands in for "not a rental" (PC purchases) so the unique key on
    // (user_id, game_id, rental_days) also dedupes PC games — MySQL treats
    // NULL as distinct from itself in unique indexes, so NULL wouldn't dedupe.
    $days = $rental_days ?: 0;

    $stmt = $pdo->prepare("SELECT cart_item_id FROM cart_items WHERE user_id = ? AND game_id = ? AND rental_days = ?");
    $stmt->execute([$user_id, $game_id, $days]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, game_id, rental_days) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $game_id, $days]);
    }
    header("Location: cart.php");
    exit;
} elseif ($action === 'remove' && !empty($_GET['cart_item_id'])) {
    $cart_item_id = (int)$_GET['cart_item_id'];
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_item_id = ? AND user_id = ?");
    $stmt->execute([$cart_item_id, $user_id]);
    header("Location: cart.php");
    exit;
}

header("Location: index.php");
exit;
?>
