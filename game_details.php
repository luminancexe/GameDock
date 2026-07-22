<?php
require_once "includes/connection.php";
include "includes/header.php";

$game_id = $_GET['id'] ?? null;

if (!$game_id) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Invalid Game ID.</div></div>";
    include "includes/footer.php";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM games WHERE game_id = ?");
$stmt->execute([$game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Game not found.</div></div>";
    include "includes/footer.php";
    exit;
}

$in_wishlist = false;
$active_rental = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT wishlist_id FROM wishlists WHERE user_id = ? AND game_id = ?");
    $stmt->execute([$_SESSION['user_id'], $game_id]);
    $in_wishlist = $stmt->fetch() !== false;

    if ($game['platform'] !== 'PC') {
        $stmt = $pdo->prepare("SELECT rental_id FROM rentals WHERE user_id = ? AND game_id = ? AND rental_status = 'active'");
        $stmt->execute([$_SESSION['user_id'], $game_id]);
        $active_rental = $stmt->fetch() !== false;
    }
}

// "Add to Cart" is only offered for platforms the checkout flow actually
// supports end to end (PC purchases and PS4/PS5 rentals). Xbox rentals have a
// pre-existing gap — rentals.console is a PS4/PS5-only ENUM — so adding one to
// the cart would silently corrupt the row at checkout; left untouched here.
$cartEligible = in_array($game['platform'], ['PC', 'PS4', 'PS5'], true);
$alreadyInCart = false;
if ($cartEligible && $game['platform'] === 'PC' && isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT 1 FROM cart_items WHERE user_id = ? AND game_id = ? AND rental_days = 0");
    $stmt->execute([$_SESSION['user_id'], $game['game_id']]);
    $alreadyInCart = $stmt->fetch() !== false;
}
?>

<div class="container mt-4">
    <div style="display: flex; gap: 1.875rem; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 18.75rem;">
            <img src="uploads/<?php echo htmlspecialchars($game['image']); ?>" alt="Game Cover" style="width: 100%; border-radius: 0.5rem;" onerror="this.src='https://via.placeholder.com/600x400?text=No+Image'">
        </div>
        <div style="flex: 2; min-width: 18.75rem;">
            <span class="tag <?php echo $game['platform'] === 'PC' ? 'own' : 'rent'; ?>" style="position: static; display: inline-block; margin-bottom: 0.75rem;"><?php echo $game['platform'] === 'PC' ? 'Buy' : 'Rent'; ?></span>
            <h2><?php echo htmlspecialchars($game['title']); ?></h2>
            <p><strong>Platform:</strong> <?php echo htmlspecialchars($game['platform']); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($game['category']); ?></p>
            <p><strong>Stock Available:</strong> <?php echo htmlspecialchars($game['stock']); ?></p>
            <p><?php echo nl2br(htmlspecialchars($game['description'])); ?></p>
            
            <div style="margin-top: 1.25rem; padding: 1.25rem; background-color: var(--secondary-bg); border-radius: 0.5rem;">
                <?php if ($game['platform'] === 'PC'): ?>
                    <h3 style="color: var(--success);"><?php echo format_price($game['purchase_price']); ?></h3>
                    <?php if ($game['stock'] <= 0): ?>
                         <button class="btn" style="background-color: var(--border); color: var(--muted); cursor: not-allowed;" disabled>Out of Stock</button>
                    <?php else: ?>
                        <a href="buy.php?id=<?php echo $game['game_id']; ?>" class="btn btn-primary">Buy Now</a>
                    <?php endif; ?>
                <?php else: ?>
                    <h3 style="color: var(--warning);"><?php echo format_price($game['rent_price'], ' / day'); ?></h3>
                    <?php if ($active_rental): ?>
                        <div style="background-color: var(--rent); color: #04191b; padding: 0.625rem; border-radius: 0.3125rem; text-align: center; margin-bottom: 0.9375rem; font-weight: bold;">
                            You are currently renting this game.
                        </div>
                    <?php endif; ?>

                    <?php if ($game['stock'] <= 0): ?>
                         <button class="btn" style="background-color: var(--border); color: var(--muted); cursor: not-allowed;" disabled>Out of Stock (Currently Rented Out)</button>
                    <?php else: ?>
                        <a href="rental_checkout.php?id=<?php echo $game['game_id']; ?>" class="btn btn-primary" style="background-color: var(--warning); color: #000;">Rent Game</a>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="wishlist_action.php?game_id=<?php echo $game['game_id']; ?>" class="btn <?php echo $in_wishlist ? 'btn-danger' : 'btn-primary'; ?>" style="margin-left: 0.625rem; <?php echo $in_wishlist ? '' : 'background-color: var(--border); color: var(--light-bg);'; ?>">
                        <?php echo $in_wishlist ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>
                    </a>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id']) && $cartEligible && $game['stock'] > 0): ?>
                    <?php if ($game['platform'] === 'PC'): ?>
                        <?php if ($alreadyInCart): ?>
                            <a href="cart.php" class="btn" style="margin-left: 0.625rem; background-color: var(--border); color: var(--light-bg);">Already in Cart &mdash; View Cart</a>
                        <?php else: ?>
                            <a href="cart_action.php?action=add&game_id=<?php echo $game['game_id']; ?>" class="btn" style="margin-left: 0.625rem; background-color: var(--border); color: var(--light-bg);">Add to Cart</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <form action="cart_action.php" method="POST" style="display: inline-flex; gap: 0.625rem; align-items: center; margin-left: 0.625rem; vertical-align: middle;">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="game_id" value="<?php echo $game['game_id']; ?>">
                            <select name="rental_days" class="form-control" style="width: auto; padding: 0.55rem 0.75rem;">
                                <option value="3">3 Days</option>
                                <option value="7">7 Days</option>
                                <option value="15">15 Days</option>
                                <option value="30">30 Days</option>
                            </select>
                            <button type="submit" class="btn" style="background-color: var(--border); color: var(--light-bg);">Add to Cart</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
