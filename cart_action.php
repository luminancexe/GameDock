<?php
require_once "includes/connection.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$game_id = $_POST['game_id'] ?? $_GET['game_id'] ?? '';
$rental_days = $_POST['rental_days'] ?? null;

if ($action === 'add' && !empty($game_id)) {
    // Add item to cart
    $cart_item_id = $game_id . ($rental_days ? "_$rental_days" : "");
    
    if (!isset($_SESSION['cart'][$cart_item_id])) {
        $_SESSION['cart'][$cart_item_id] = [
            'game_id' => $game_id,
            'rental_days' => $rental_days
        ];
    }
    header("Location: cart.php");
    exit;
} elseif ($action === 'remove' && !empty($_GET['cart_id'])) {
    // Remove item from cart
    $cart_id = $_GET['cart_id'];
    if (isset($_SESSION['cart'][$cart_id])) {
        unset($_SESSION['cart'][$cart_id]);
    }
    header("Location: cart.php");
    exit;
}

header("Location: index.php");
exit;
?>
