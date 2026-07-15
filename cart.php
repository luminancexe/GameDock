<?php
require_once "includes/connection.php";
include "includes/header.php";

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
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
                <?php 
                foreach ($cart_items as $cart_id => $item) {
                    $stmt = $pdo->prepare("SELECT * FROM games WHERE game_id = ?");
                    $stmt->execute([$item['game_id']]);
                    $game = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($game) {
                        $price = 0;
                        if ($game['platform'] === 'PC') {
                            $price = $game['purchase_price'];
                        } else {
                            $price = $game['rent_price'] * $item['rental_days'];
                        }
                        $total_price += $price;
                        ?>
                        <div class="game-card" style="display: flex; padding: 0.9375rem; margin-bottom: 0.9375rem; align-items: center;">
                            <img src="uploads/<?php echo htmlspecialchars($game['image']); ?>" alt="Game" style="width: 6.25rem; height: 6.25rem; border-radius: 0.5rem; margin-right: 1.25rem; object-fit: cover;">
                            <div style="flex-grow: 1;">
                                <h4 style="margin: 0; color: var(--light-bg);"><?php echo htmlspecialchars($game['title']); ?> (<?php echo htmlspecialchars($game['platform']); ?>)</h4>
                                <?php if ($game['platform'] !== 'PC'): ?>
                                    <p style="color: var(--muted); margin: 0.3125rem 0 0 0; font-size: 0.9em;">Rental Duration: <?php echo htmlspecialchars($item['rental_days']); ?> Days</p>
                                <?php endif; ?>
                            </div>
                            <div style="text-align: right;">
                                <h3 style="margin: 0; color: <?php echo $game['platform'] === 'PC' ? 'var(--success)' : 'var(--warning)'; ?>;"><?php echo format_price($price); ?></h3>
                                <a href="cart_action.php?action=remove&cart_id=<?php echo urlencode($cart_id); ?>" style="color: var(--danger); font-size: 0.9em; text-decoration: none; display: inline-block; margin-top: 0.625rem;">Remove</a>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
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
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="checkout_process.php" method="POST">
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Proceed to Checkout</button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary" style="width: 100%; text-align: center;">Login to Checkout</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
