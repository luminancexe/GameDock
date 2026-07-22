<?php
require_once "includes/connection.php";
require_once "includes/auth_check.php";
requireLogin();

// User Details
$stmt = $pdo->prepare("SELECT fullname, email, phone, profile_pic, billing_card_name, billing_card_last4, billing_card_brand, billing_card_expiry FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Purchases
$stmt = $pdo->prepare("SELECT p.*, g.title, g.image FROM purchases p JOIN games g ON p.game_id = g.game_id WHERE p.user_id = ? ORDER BY p.purchase_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Rentals
$stmt = $pdo->prepare("SELECT r.*, g.title, g.image FROM rentals r JOIN games g ON r.game_id = g.game_id WHERE r.user_id = ? ORDER BY r.start_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Submissions
$stmt = $pdo->prepare("SELECT * FROM sell_games WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Wishlist count — the full list now lives on its own page, linked from here
// instead of the nav bar.
$stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$wishlistCount = (int)$stmt->fetchColumn();

include "includes/header.php";
?>

<div class="container mt-4">
    <h2>My Profile</h2>
    <div style="background-color: var(--secondary-bg); padding: 1.25rem; border-radius: 0.5rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;">
        <img src="uploads/avatars/<?php echo htmlspecialchars($user['profile_pic'] ?? 'default.png'); ?>" alt="Your avatar" style="width: 4.5rem; height: 4.5rem; border-radius: 50%; object-fit: cover; border: 0.125rem solid var(--accent); flex-shrink: 0;">
        <div style="flex: 1; min-width: 12rem;">
            <h3>Welcome, <?php echo htmlspecialchars($user['fullname']); ?></h3>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Phone: <?php echo htmlspecialchars($user['phone']); ?></p>
        </div>
        <?php $hasCustomAvatar = ($user['profile_pic'] ?? 'default.png') !== 'default.png'; ?>
        <a href="avatar_builder.php" class="btn btn-primary" style="align-self: center;"><?php echo $hasCustomAvatar ? 'Change Avatar' : 'Choose Your Avatar'; ?></a>
    </div>

    <h3>Billing Information</h3>
    <?php if (!empty($user['billing_card_last4'])): ?>
        <div style="background-color: var(--secondary-bg); padding: 1.25rem; border-radius: 0.5rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 12rem;">
                <p style="margin: 0 0 0.3rem 0; font-family: 'Unbounded', sans-serif; letter-spacing: 0.05em;">
                    <?php echo htmlspecialchars($user['billing_card_brand']); ?> &nbsp; **** **** **** <?php echo htmlspecialchars($user['billing_card_last4']); ?>
                </p>
                <p style="margin: 0; color: var(--muted); font-size: 0.85em;">
                    <?php echo htmlspecialchars($user['billing_card_name']); ?><?php if (!empty($user['billing_card_expiry'])): ?> &middot; Expires <?php echo htmlspecialchars($user['billing_card_expiry']); ?><?php endif; ?>
                </p>
            </div>
            <a href="billing_action.php?action=remove" class="btn btn-danger confirm-action">Remove Saved Card</a>
        </div>
    <?php else: ?>
        <p style="color: var(--muted); margin-bottom: 1.25rem;">No billing information saved. You can save a card at checkout next time you buy a PC game.</p>
    <?php endif; ?>

    <h3>My Wishlist</h3>
    <div style="background-color: var(--secondary-bg); padding: 1.25rem; border-radius: 0.5rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1.25rem; flex-wrap: wrap;">
        <p style="margin: 0; color: var(--muted);">
            <?php echo $wishlistCount > 0 ? "You have {$wishlistCount} game" . ($wishlistCount === 1 ? '' : 's') . " saved." : "No games saved yet."; ?>
        </p>
        <a href="wishlist.php" class="btn btn-primary">View Wishlist<?php echo $wishlistCount > 0 ? " ({$wishlistCount})" : ''; ?></a>
    </div>

    <h3>Purchase History (PC Games)</h3>
    <?php if(count($purchases) > 0): ?>
        <table style="width: 100%; text-align: left; margin-bottom: 20px; border-collapse: collapse;">
            <tr style="background-color: #374151;">
                <th style="padding: 10px;">Game</th>
                <th style="padding: 10px;">Price</th>
                <th style="padding: 10px;">Date</th>
                <th style="padding: 10px;">Status</th>
                <th style="padding: 10px;">Receipt</th>
            </tr>
            <?php foreach($purchases as $p): ?>
            <tr style="border-bottom: 1px solid #4B5563;">
                <td style="padding: 10px;"><?php echo htmlspecialchars($p['title']); ?></td>
                <td style="padding: 10px;"><?php echo format_price($p['total_price']); ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($p['purchase_date']); ?></td>
                <td style="padding: 10px; color: var(--success);"><?php echo htmlspecialchars($p['order_status']); ?></td>
                <td style="padding: 10px;"><a href="receipt.php?type=buy&id=<?php echo $p['purchase_id']; ?>" style="text-decoration: underline;">View</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No purchases yet.</p>
    <?php endif; ?>

    <h3>Rental History (PS Games)</h3>
    <?php if(count($rentals) > 0): ?>
        <table style="width: 100%; text-align: left; margin-bottom: 20px; border-collapse: collapse;">
            <tr style="background-color: #374151;">
                <th style="padding: 10px;">Game</th>
                <th style="padding: 10px;">Console</th>
                <th style="padding: 10px;">Days</th>
                <th style="padding: 10px;">End Date</th>
                <th style="padding: 10px;">Status</th>
                <th style="padding: 10px;">Receipt</th>
            </tr>
            <?php foreach($rentals as $r): ?>
            <tr style="border-bottom: 1px solid #4B5563;">
                <td style="padding: 10px;"><?php echo htmlspecialchars($r['title']); ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($r['console']); ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($r['rental_days']); ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($r['end_date']); ?></td>
                <td style="padding: 10px; color: var(--warning);"><?php echo htmlspecialchars($r['rental_status']); ?></td>
                <td style="padding: 10px;"><a href="receipt.php?type=rent&id=<?php echo $r['rental_id']; ?>" style="text-decoration: underline;">View</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No rentals yet.</p>
    <?php endif; ?>

    <h3>Games Submitted for Sale</h3>
    <?php if(count($submissions) > 0): ?>
        <table style="width: 100%; text-align: left; margin-bottom: 1.25rem; border-collapse: collapse;">
            <tr style="background-color: var(--surface);">
                <th style="padding: 0.625rem;">Title</th>
                <th style="padding: 0.625rem;">Platform</th>
                <th style="padding: 0.625rem;">Price</th>
                <th style="padding: 0.625rem;">Status</th>
            </tr>
            <?php foreach($submissions as $s): ?>
            <tr style="border-bottom: 0.0625rem solid var(--border);">
                <td style="padding: 0.625rem;"><?php echo htmlspecialchars($s['title']); ?></td>
                <td style="padding: 0.625rem;"><?php echo htmlspecialchars($s['platform']); ?></td>
                <td style="padding: 0.625rem;"><?php echo format_price($s['asking_price']); ?></td>
                <td style="padding: 0.625rem; color: var(--accent);"><?php echo htmlspecialchars($s['status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No games submitted for sale.</p>
    <?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>
