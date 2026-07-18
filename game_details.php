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
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
