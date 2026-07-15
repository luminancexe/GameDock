<?php
require_once "includes/connection.php";
include "includes/header.php";

$wishlist = [];
$active_rentals = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT game_id FROM wishlists WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $wishlist = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
    $stmt = $pdo->prepare("SELECT game_id FROM rentals WHERE user_id = ? AND rental_status = 'active'");
    $stmt->execute([$_SESSION['user_id']]);
    $active_rentals = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

// Fetch PlayStation rentals
$stmt = $pdo->prepare("SELECT * FROM games WHERE (platform = 'PS4' OR platform = 'PS5') AND status = 'Available'");
$stmt->execute();
$ps_games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2>PlayStation Rentals</h2>
    <div class="game-grid">
        <?php if (count($ps_games) > 0): ?>
            <?php foreach ($ps_games as $game): ?>
                <div class="game-card rent">
                    <span class="tag rent">Rent</span>
                    <img src="uploads/<?php echo htmlspecialchars($game['image']); ?>" alt="Game Cover" onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                    <div class="game-card-content">
                        <h3><?php echo htmlspecialchars($game['title']); ?> <small>(<?php echo htmlspecialchars($game['platform']); ?>)</small></h3>
                        <p class="price"><?php echo format_price($game['rent_price'], ' / day'); ?></p>

                        <?php if(isset($_SESSION['user_id'])): ?>
                            <?php $in_wishlist = in_array($game['game_id'], $wishlist); ?>
                            <a href="wishlist_action.php?game_id=<?php echo $game['game_id']; ?>" class="btn <?php echo $in_wishlist ? 'btn-danger' : 'btn-primary'; ?>" style="margin-bottom: 10px; display: block; text-align: center; <?php echo $in_wishlist ? '' : 'background-color: var(--border); color: var(--light-bg);'; ?>">
                                <?php echo $in_wishlist ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>
                            </a>

                            <?php if(in_array($game['game_id'], $active_rentals)): ?>
                                <div style="background-color: var(--rent); color: #04191b; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 10px; font-weight: bold;">
                                    Currently Rented by You
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($game['stock'] <= 0): ?>
                            <button class="btn" style="background-color: var(--border); color: var(--muted); cursor: not-allowed; display: block; width: 100%;" disabled>Out of Stock (Rented Out)</button>
                        <?php else: ?>
                            <a href="game_details.php?id=<?php echo $game['game_id']; ?>" class="btn btn-primary" style="background-color: var(--warning); color: #000; display: block; text-align: center;">View Rental Options</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No PlayStation games available for rental at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<?php include "includes/footer.php"; ?>
