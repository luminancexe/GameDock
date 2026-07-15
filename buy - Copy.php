<?php
require_once "includes/connection.php";
require_once "includes/auth_check.php";
requireLogin();

$game_id = $_GET['id'] ?? null;
if (!$game_id) {
    header("Location: pc_games.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM games WHERE game_id = ? AND platform = 'PC'");
$stmt->execute([$game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    die("Game not found.");
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST['payment_method'];
    $stmt = $pdo->prepare("INSERT INTO purchases (user_id, game_id, total_price, purchase_date, payment_method, payment_status, order_status) VALUES (?, ?, ?, NOW(), ?, 'Paid', 'Completed')");
    if ($stmt->execute([$_SESSION['user_id'], $game_id, $game['purchase_price'], $payment_method])) {
        $message = "Purchase successful! Check your profile for details.";
    } else {
        $message = "Error processing purchase.";
    }
}
include "includes/header.php";
?>

<div class="container">
    <div class="form-container">
        <h2>Buy <?php echo htmlspecialchars($game['title']); ?></h2>
        <?php if($message): ?>
            <div class="alert alert-success"><?php echo $message; ?> <a href="profile.php">Go to Profile</a></div>
        <?php else: ?>
            <p><strong>Price:</strong> <?php echo format_price($game['purchase_price']); ?></p>
            <form action="buy.php?id=<?php echo $game_id; ?>" method="POST">
                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method" class="form-control" required>
                        <option value="Credit Card">Credit Card</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 confirm-action" style="width: 100%;">Confirm Purchase</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include "includes/footer.php"; ?>
