<?php
require_once "includes/connection.php";
require_once "includes/auth_check.php";
requireLogin();
include "includes/header.php";

$stmt = $pdo->prepare("SELECT g.* FROM games g JOIN wishlists w ON g.game_id = w.game_id WHERE w.user_id = ? ORDER BY w.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$wishlist_games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2>My Wishlist</h2>
    <div class="game-grid">
        <?php if (count($wishlist_games) > 0): ?>
            <?php foreach ($wishlist_games as $game): ?>
                <div class="game-card">
                    <img src="uploads/<?php echo htmlspecialchars($game['image']); ?>" alt="Game Cover" onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                    <div class="game-card-content">
                        <h3><?php echo htmlspecialchars($game['title']); ?> <small>(<?php echo htmlspecialchars($game['platform']); ?>)</small></h3>
                        <p class="price"><?php echo $game['platform'] === 'PC' ? format_price($game['purchase_price']) : format_price($game['rent_price'], ' / day'); ?></p>
                        <div style="display:flex; gap: 10px; margin-top: auto;">
                            <a href="game_details.php?id=<?php echo $game['game_id']; ?>" class="btn btn-primary" style="flex:1;">View</a>
                            <a href="wishlist_action.php?game_id=<?php echo $game['game_id']; ?>" class="btn btn-danger" style="flex:1;">Remove</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Your wishlist is empty.</p>
        <?php endif; ?>
    </div>
</div>

<?php include "includes/footer.php"; ?>
