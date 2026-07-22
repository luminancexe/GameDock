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

// Pre-fill the form if this user already has a saved card on file.
$stmt = $pdo->prepare("SELECT billing_card_name, billing_card_last4, billing_card_brand, billing_card_expiry FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$savedCard = $stmt->fetch(PDO::FETCH_ASSOC);
$hasSavedCard = !empty($savedCard['billing_card_last4']);

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // In a real app, we would process the CC securely here.
    // "Card" is the closest valid value in the purchases.payment_method ENUM
    // ('Cash','bKash','Nagad','Card','Demo Payment') — "Credit Card" isn't a
    // member, which was silently being stored as an empty string.
    $payment_method = "Card";
    $stmt = $pdo->prepare("INSERT INTO purchases (user_id, game_id, total_price, purchase_date, payment_method, payment_status, order_status) VALUES (?, ?, ?, NOW(), ?, 'Paid', 'Completed')");
    if ($stmt->execute([$_SESSION['user_id'], $game_id, $game['purchase_price'], $payment_method])) {
        $purchase_id = $pdo->lastInsertId();

        // "Save Billing Information" — never store the full card number or CVV,
        // even when saving. Only the cardholder name, last 4 digits, inferred
        // brand, and expiry are kept, which is enough to show "Visa ending in
        // 4242" on the profile page without holding data that could actually be
        // used to charge the card.
        if (isset($_POST['save_card'])) {
            $cardNumberDigits = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
            if (strlen($cardNumberDigits) >= 13 && strlen($cardNumberDigits) <= 19) {
                $last4 = substr($cardNumberDigits, -4);
                $firstDigit = $cardNumberDigits[0];
                $brand = match (true) {
                    $firstDigit === '4' => 'Visa',
                    $firstDigit === '5' => 'Mastercard',
                    $firstDigit === '3' => 'Amex',
                    $firstDigit === '6' => 'Discover',
                    default => 'Card',
                };
                $cardName = trim($_POST['card_name'] ?? '');
                $cardExpiry = trim($_POST['card_expiry'] ?? '');

                $stmt = $pdo->prepare("UPDATE users SET billing_card_name = ?, billing_card_last4 = ?, billing_card_brand = ?, billing_card_expiry = ? WHERE user_id = ?");
                $stmt->execute([$cardName, $last4, $brand, $cardExpiry, $_SESSION['user_id']]);
            }
            // $cardNumberDigits and the raw $_POST card fields are never persisted beyond this point.
        }

        header("Location: receipt.php?type=buy&id=" . $purchase_id);
        exit;
    } else {
        $message = "Error processing purchase.";
    }
}
include "includes/header.php";
?>

<div class="container">
    <div class="form-container" style="max-width: 600px;">
        <h2 style="color: var(--own);">Checkout</h2>
        <?php if($message): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div style="display: flex; gap: 20px; align-items: flex-start; margin-bottom: 25px; padding-bottom: 25px; border-bottom: 1px solid var(--border);">
            <img src="uploads/<?php echo htmlspecialchars($game['image']); ?>" alt="Game Cover" style="width: 80px; height: 100px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border);">
            <div>
                <h3 style="margin: 0 0 5px 0;"><?php echo htmlspecialchars($game['title']); ?></h3>
                <p style="color: var(--muted); margin: 0 0 10px 0;">Platform: PC (Digital Ownership)</p>
                <h4 style="margin: 0; color: var(--success); font-family: 'Unbounded', sans-serif;"><?php echo format_price($game['purchase_price']); ?></h4>
            </div>
        </div>

        <form action="buy.php?id=<?php echo $game_id; ?>" method="POST">
            <h4 style="margin-top: 0; margin-bottom: 15px;">Payment Details</h4>

            <?php if ($hasSavedCard): ?>
                <div class="alert alert-success" style="margin-bottom: 15px;">
                    You have a saved card on file: <?php echo htmlspecialchars($savedCard['billing_card_brand']); ?> ending in <?php echo htmlspecialchars($savedCard['billing_card_last4']); ?>. Manage it from your <a href="profile.php">profile</a>.
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Name on Card</label>
                <input type="text" name="card_name" class="form-control" placeholder="e.g. John Doe" value="<?php echo htmlspecialchars($savedCard['billing_card_name'] ?? ''); ?>" required>
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

            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="save_card" id="save_card" style="width: auto;">
                <label for="save_card" style="margin: 0; font-weight: normal;">Save billing information for next time</label>
            </div>
            <p style="color: var(--muted); font-size: 12px; margin-top: -8px;">Only your name, card brand, last 4 digits, and expiry are saved — never the full card number or CVV.</p>

            <button type="submit" class="btn btn-primary confirm-action" style="width: 100%; margin-top: 15px; font-size: 16px; padding: 14px;">Pay <?php echo format_price($game['purchase_price']); ?></button>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>
