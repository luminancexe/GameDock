<?php
require_once "includes/connection.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_items = $_SESSION['cart'];

try {
    $pdo->beginTransaction();

    foreach ($cart_items as $cart_id => $item) {
        $stmt = $pdo->prepare("SELECT * FROM games WHERE game_id = ?");
        $stmt->execute([$item['game_id']]);
        $game = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($game) {
            if ($game['platform'] === 'PC') {
                // Process PC Purchase
                $price = $game['purchase_price'];
                $insert_stmt = $pdo->prepare("INSERT INTO purchases (user_id, game_id, total_price, payment_method, payment_status, order_status) VALUES (?, ?, ?, 'Demo Payment', 'paid', 'completed')");
                $insert_stmt->execute([$user_id, $game['game_id'], $price]);
                
                // Decrement stock
                $update_stock = $pdo->prepare("UPDATE games SET stock = stock - 1 WHERE game_id = ?");
                $update_stock->execute([$game['game_id']]);
                
            } else {
                // Process PS Rental
                $rental_days = $item['rental_days'];
                $price = $game['rent_price'] * $rental_days;
                $start_date = date("Y-m-d");
                $end_date = date("Y-m-d", strtotime("+$rental_days days"));
                
                $insert_stmt = $pdo->prepare("INSERT INTO rentals (user_id, game_id, console, rental_days, start_date, end_date, total_price, payment_method, payment_status, rental_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Demo Payment', 'paid', 'active')");
                $insert_stmt->execute([$user_id, $game['game_id'], $game['platform'], $rental_days, $start_date, $end_date, $price]);
                
                // Decrement stock
                $update_stock = $pdo->prepare("UPDATE games SET stock = stock - 1 WHERE game_id = ?");
                $update_stock->execute([$game['game_id']]);
            }
        }
    }

    $pdo->commit();
    
    // Clear cart
    unset($_SESSION['cart']);
    
    // Redirect to profile
    header("Location: profile.php");
    exit;

} catch(PDOException $e) {
    $pdo->rollBack();
    die("Checkout failed: " . $e->getMessage());
}
?>
