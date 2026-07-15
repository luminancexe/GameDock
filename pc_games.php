<?php
require_once "includes/connection.php";
include "includes/header.php";

$wishlist = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT game_id FROM wishlists WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $wishlist = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

// Fetch PC games
$stmt = $pdo->prepare("SELECT * FROM games WHERE platform = 'PC' AND status = 'Available'");
$stmt->execute();
$pc_games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2>PC Games (Offline Supported)</h2>
    <div class="game-grid">
        <?php if (count($pc_games) > 0): ?>
            <?php foreach ($pc_games as $game): ?>
                <div class="game-card own">
                    <span class="tag own">Own</span>
                    <img src="uploads/<?php echo htmlspecialchars($game['image']); ?>" alt="Game Cover" onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                    <div class="game-card-content">
                        <h3><?php echo htmlspecialchars($game['title']); ?></h3>
                        <p class="price"><?php echo format_price($game['purchase_price']); ?></p>

                        <?php if(isset($_SESSION['user_id'])): ?>
                            <?php $in_wishlist = in_array($game['game_id'], $wishlist); ?>
                            <a href="wishlist_action.php?game_id=<?php echo $game['game_id']; ?>" class="btn <?php echo $in_wishlist ? 'btn-danger' : 'btn-primary'; ?>" style="margin-bottom: 0.625rem; display: block; text-align: center; <?php echo $in_wishlist ? '' : 'background-color: var(--border); color: var(--light-bg);'; ?>">
                                <?php echo $in_wishlist ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>
                            </a>
                        <?php endif; ?>

                        <a href="game_details.php?id=<?php echo $game['game_id']; ?>" class="btn btn-primary" style="display: block; text-align: center;">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No PC games available at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<?php include "includes/footer.php"; ?>
