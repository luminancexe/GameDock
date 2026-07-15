<?php
require_once "includes/connection.php";
include "includes/header.php";

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_price = 0;
?>

<div class="container mt-4">
    <h2 class="text-center" style="color: var(--accent); margin-bottom: 30px;">My Cart</h2>

    <?php if (empty($cart_items)): ?>
        <div class="form-container" style="text-align: center;">
            <h3 style="color: #9CA3AF;">Your cart is empty.</h3>
            <p style="color: #6B7280; margin-bottom: 20px;">Find some epic games to add!</p>
            <a href="pc_games.php" class="btn btn-primary" style="margin-right: 10px;">Browse PC Games</a>
            <a href="ps_rentals.php" class="btn btn-primary" style="background-color: var(--warning); color: #000;">Rent PS Games</a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">
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
                        <div class="game-card" style="display: flex; padding: 15px; margin-bottom: 15px; align-items: center;">
                            <img src="uploads/<?php echo htmlspecialchars($game['image']); ?>" alt="Game" style="width: 100px; height: 100px; border-radius: 8px; margin-right: 20px; object-fit: cover;">
                            <div style="flex-grow: 1;">
                                <h4 style="margin: 0; color: white;"><?php echo htmlspecialchars($game['title']); ?> (<?php echo htmlspecialchars($game['platform']); ?>)</h4>
                                <?php if ($game['platform'] !== 'PC'): ?>
                                    <p style="color: #9CA3AF; margin: 5px 0 0 0; font-size: 0.9em;">Rental Duration: <?php echo htmlspecialchars($item['rental_days']); ?> Days</p>
                                <?php endif; ?>
                            </div>
                            <div style="text-align: right;">
                                <h3 style="margin: 0; color: <?php echo $game['platform'] === 'PC' ? 'var(--success)' : 'var(--warning)'; ?>;">$<?php echo number_format($price, 2); ?></h3>
                                <a href="cart_action.php?action=remove&cart_id=<?php echo urlencode($cart_id); ?>" style="color: var(--danger); font-size: 0.9em; text-decoration: none; display: inline-block; margin-top: 10px;">Remove</a>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

            <!-- Checkout Summary -->
            <div>
                <div class="form-container" style="margin: 0; position: sticky; top: 100px;">
                    <h3 style="margin-top: 0; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px;">Order Summary</h3>
                    
                    <div style="display: flex; justify-content: space-between; margin: 20px 0;">
                        <span style="color: #D1D5DB;">Items (<?php echo count($cart_items); ?>)</span>
                        <span style="color: white; font-weight: bold;">$<?php echo number_format($total_price, 2); ?></span>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                        <span style="color: var(--accent); font-size: 1.2em; font-weight: bold;">Grand Total</span>
                        <span style="color: var(--accent); font-size: 1.2em; font-weight: bold;">$<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="checkout_process.php" method="POST">
                            <button type="submit" class="btn btn-primary w-full">Proceed to Checkout</button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary w-full" style="text-align: center;">Login to Checkout</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
