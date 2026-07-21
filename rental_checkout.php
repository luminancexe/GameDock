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
        $rental_id = $pdo->lastInsertId();
        
        // Decrement stock
        $stmt_update = $pdo->prepare("UPDATE games SET stock = stock - 1 WHERE game_id = ?");
        $stmt_update->execute([$game_id]);
        
        $pdo->commit();
        header("Location: receipt.php?type=rent&id=" . $rental_id);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Error processing rental: " . $e->getMessage();
    }
}
include "includes/header.php";
?>

<div class="container">
    <div class="form-container" style="max-width: 600px;">
        <h2 style="color: var(--rent);">Rental Checkout</h2>
        <?php if($message): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div style="display: flex; gap: 20px; align-items: flex-start; margin-bottom: 25px; padding-bottom: 25px; border-bottom: 1px solid var(--border);">
            <img src="uploads/<?php echo htmlspecialchars($game['image']); ?>" alt="Game Cover" style="width: 80px; height: 100px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border);">
            <div>
                <h3 style="margin: 0 0 5px 0;"><?php echo htmlspecialchars($game['title']); ?></h3>
                <p style="color: var(--muted); margin: 0 0 10px 0;">Platform: <?php echo htmlspecialchars($game['platform']); ?> (Physical Disc Rental)</p>
                <h4 style="margin: 0; color: var(--warning); font-family: 'Unbounded', sans-serif;">
                    <?php echo currency_symbol(); ?><span id="daily-price"><?php echo number_format(convert_price($game['rent_price']), 2); ?></span> / day
                </h4>
            </div>
        </div>

        <form action="rental_checkout.php?id=<?php echo $game_id; ?>" method="POST">
            <div class="form-group">
                <label>Rental Duration (Days)</label>
                <select name="rental_days" id="rental-days" class="form-control" onchange="calculateTotal()" required style="font-size: 16px; padding: 12px;">
                    <option value="3">3 Days</option>
                    <option value="7">7 Days</option>
                    <option value="15">15 Days</option>
                    <option value="30">30 Days</option>
                </select>
            </div>
            
            <div style="background-color: var(--primary-bg); padding: 15px; border-radius: 6px; border: 1px solid var(--border); margin-bottom: 20px;">
                <h3 id="total-display" style="color: var(--warning); margin: 0; text-align: right;">
                    Total: <?php echo currency_symbol(); ?><?php echo number_format(convert_price($game['rent_price']) * 3, 2); ?>
                </h3>
            </div>

            <h4 style="margin-top: 0; margin-bottom: 15px;">Payment Details</h4>
            
            <div class="form-group">
                <label>Name on Card</label>
                <input type="text" name="card_name" class="form-control" placeholder="e.g. John Doe" required>
            </div>
            
            <div class="form-group">
                <label>Card Number</label>
                <input type="text" name="card_number" class="form-control" placeholder="0000 0000 0000 0000" pattern="\d{16}" title="16 digit card number" required>
            </div>
            
            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Expiry Date</label>
                    <input type="text" name="card_expiry" class="form-control" placeholder="MM/YY" pattern="\d{2}/\d{2}" title="Format: MM/YY" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>CVV</label>
                    <input type="text" name="card_cvv" class="form-control" placeholder="123" pattern="\d{3,4}" title="3 or 4 digit CVV" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary confirm-action" style="background-color: var(--warning); color: #000; width: 100%; margin-top: 15px; font-size: 16px; padding: 14px;">Confirm Rental</button>
        </form>
    </div>
</div>

<script>
function calculateTotal() {
    const daily = parseFloat(document.getElementById('daily-price').innerText);
    const days = parseInt(document.getElementById('rental-days').value);
    document.getElementById('total-display').innerText = "Total: <?php echo currency_symbol(); ?>" + (daily * days).toFixed(2);
}
</script>

<?php include "includes/footer.php"; ?>
