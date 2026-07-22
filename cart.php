<?php
require_once "includes/connection.php";
require_once "includes/auth_check.php";
requireLogin();
include "includes/header.php";

$stmt = $pdo->prepare("SELECT ci.cart_item_id, ci.rental_days, g.* FROM cart_items ci JOIN games g ON ci.game_id = g.game_id WHERE ci.user_id = ? ORDER BY ci.added_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_price = 0;
?>

<div class="container mt-4">
    <h2 class="text-center" style="color: var(--accent); margin-bottom: 1.875rem;">My Cart</h2>

    <?php if (empty($cart_items)): ?>
        <div class="form-container" style="text-align: center;">
            <h3 style="color: var(--muted);">Your cart is empty.</h3>
            <p style="color: var(--muted); margin-bottom: 1.25rem;">Find some epic games to add!</p>
            <a href="pc_games.php" class="btn btn-primary" style="margin-right: 0.625rem;">Browse PC Games</a>
            <a href="ps_rentals.php" class="btn btn-primary" style="background-color: var(--warning); color: #000;">Rent PS Games</a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem;">
            <!-- Cart Items -->
            <div>
                <?php foreach ($cart_items as $item): ?>
                    <?php
                    if ($item['platform'] === 'PC') {
                        $price = $item['purchase_price'];
                    } else {
                        $price = $item['rent_price'] * $item['rental_days'];
                    }
                    $total_price += $price;
                    ?>
                    <div class="game-card" style="display: flex; padding: 0.9375rem; margin-bottom: 0.9375rem; align-items: center;">
                        <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="Game" style="width: 6.25rem; height: 6.25rem; border-radius: 0.5rem; margin-right: 1.25rem; object-fit: cover;">
                        <div style="flex-grow: 1;">
                            <h4 style="margin: 0; color: var(--light-bg);"><?php echo htmlspecialchars($item['title']); ?> (<?php echo htmlspecialchars($item['platform']); ?>)</h4>
                            <?php if ($item['platform'] !== 'PC'): ?>
                                <p style="color: var(--muted); margin: 0.3125rem 0 0 0; font-size: 0.9em;">Rental Duration: <?php echo (int)$item['rental_days']; ?> Days</p>
                            <?php endif; ?>
                        </div>
                        <div style="text-align: right;">
                            <h3 style="margin: 0; color: <?php echo $item['platform'] === 'PC' ? 'var(--success)' : 'var(--warning)'; ?>;"><?php echo format_price($price); ?></h3>
                            <a href="cart_action.php?action=remove&cart_item_id=<?php echo $item['cart_item_id']; ?>" style="color: var(--danger); font-size: 0.9em; text-decoration: none; display: inline-block; margin-top: 0.625rem;">Remove</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Checkout Summary -->
            <div>
                <div class="form-container" style="margin: 0; position: sticky; top: 6.25rem;">
                    <h3 style="margin-top: 0; border-bottom: 0.0625rem solid var(--border); padding-bottom: 0.9375rem;">Order Summary</h3>

                    <div style="display: flex; justify-content: space-between; margin: 1.25rem 0;">
                        <span style="color: var(--muted);">Items (<?php echo count($cart_items); ?>)</span>
                        <span style="color: var(--light-bg); font-weight: bold;"><?php echo format_price($total_price); ?></span>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.25rem; border-top: 0.0625rem solid var(--border); padding-top: 0.9375rem;">
                        <span style="color: var(--accent); font-size: 1.2em; font-weight: bold;">Grand Total</span>
                        <span style="color: var(--accent); font-size: 1.2em; font-weight: bold;"><?php echo format_price($total_price); ?></span>
                    </div>

                    <form action="checkout_process.php" method="POST">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Proceed to Checkout</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
