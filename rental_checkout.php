<?php
require_once "includes/connection.php";
require_once "includes/auth_check.php";
requireLogin();

$game_id = $_GET['id'] ?? null;
if (!$game_id) {
    header("Location: ps_rentals.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM games WHERE game_id = ? AND (platform = 'PS4' OR platform = 'PS5')");
$stmt->execute([$game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    die("Game not found.");
}

if ($game['stock'] <= 0) {
    die("Sorry, this game is out of stock and currently fully rented out.");
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rental_days = (int)$_POST['rental_days'];
    $total_price = $rental_days * $game['rent_price'];
    
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime("+$rental_days days"));
    
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO rentals (user_id, game_id, console, rental_days, start_date, end_date, total_price, payment_status, rental_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Paid', 'Active')");
        $stmt->execute([$_SESSION['user_id'], $game_id, $game['platform'], $rental_days, $start_date, $end_date, $total_price]);
        
        // Decrement stock
        $stmt_update = $pdo->prepare("UPDATE games SET stock = stock - 1 WHERE game_id = ?");
        $stmt_update->execute([$game_id]);
        
        $pdo->commit();
        $message = "Rental successful! Check your profile for details.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Error processing rental: " . $e->getMessage();
    }
}
include "includes/header.php";
?>

<div class="container">
    <div class="form-container">
        <h2>Rent <?php echo htmlspecialchars($game['title']); ?></h2>
        <?php if($message): ?>
            <div class="alert alert-success"><?php echo $message; ?> <a href="profile.php">Go to Profile</a></div>
        <?php else: ?>
            <p><strong>Daily Price:</strong> <?php echo currency_symbol(); ?><span id="daily-price"><?php echo number_format(convert_price($game['rent_price']), 2); ?></span></p>
            <form action="rental_checkout.php?id=<?php echo $game_id; ?>" method="POST">
                <div class="form-group">
                    <label>Rental Duration (Days)</label>
                    <select name="rental_days" id="rental-days" class="form-control" onchange="calculateTotal()" required>
                        <option value="3">3 Days</option>
                        <option value="7">7 Days</option>
                        <option value="15">15 Days</option>
                        <option value="30">30 Days</option>
                    </select>
                </div>
                <h3 id="total-display" style="color: var(--warning); margin-bottom: 0.9375rem;">Total: <?php echo currency_symbol(); ?><?php echo number_format(convert_price($game['rent_price']) * 3, 2); ?></h3>
                <button type="submit" class="btn btn-primary confirm-action" style="background-color: var(--warning); color: #000; width: 100%;">Confirm Rental</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
function calculateTotal() {
    const daily = parseFloat($('#daily-price').text());
    const days = parseInt($('#rental-days').val());
    $('#total-display').text("Total: <?php echo currency_symbol(); ?>" + (daily * days).toFixed(2));
}
</script>

<?php include "includes/footer.php"; ?>
