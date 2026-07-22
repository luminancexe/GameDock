<?php
require_once "includes/connection.php";
require_once "includes/auth_check.php";
requireLogin();

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT ci.game_id, ci.rental_days, g.platform, g.purchase_price, g.rent_price FROM cart_items ci JOIN games g ON ci.game_id = g.game_id WHERE ci.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

try {
    $pdo->beginTransaction();

    foreach ($cart_items as $item) {
        if ($item['platform'] === 'PC') {
            // Process PC Purchase
            $price = $item['purchase_price'];
            $insert_stmt = $pdo->prepare("INSERT INTO purchases (user_id, game_id, total_price, payment_method, payment_status, order_status) VALUES (?, ?, ?, 'Demo Payment', 'paid', 'completed')");
            $insert_stmt->execute([$user_id, $item['game_id'], $price]);
        } else {
            // Process PS Rental
            $rental_days = (int)$item['rental_days'];
            $price = $item['rent_price'] * $rental_days;
            $start_date = date("Y-m-d");
            $end_date = date("Y-m-d", strtotime("+$rental_days days"));

            $insert_stmt = $pdo->prepare("INSERT INTO rentals (user_id, game_id, console, rental_days, start_date, end_date, total_price, payment_method, payment_status, rental_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Demo Payment', 'paid', 'active')");
            $insert_stmt->execute([$user_id, $item['game_id'], $item['platform'], $rental_days, $start_date, $end_date, $price]);
        }

        // Decrement stock
        $update_stock = $pdo->prepare("UPDATE games SET stock = stock - 1 WHERE game_id = ?");
        $update_stock->execute([$item['game_id']]);
    }

    // Clear the cart now that every item became a purchase/rental
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $pdo->commit();

    // Redirect to profile
    header("Location: profile.php");
    exit;

} catch(PDOException $e) {
    $pdo->rollBack();
    die("Checkout failed: " . $e->getMessage());
}
?>
