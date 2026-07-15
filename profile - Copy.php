<?php
require_once "includes/connection.php";
require_once "includes/auth_check.php";
requireLogin();

// User Details
$stmt = $pdo->prepare("SELECT fullname, email, phone FROM users WHERE user_id = ?");
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

include "includes/header.php";
?>

<div class="container mt-4">
    <h2>My Profile</h2>
    <div style="background-color: var(--secondary-bg); padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3>Welcome, <?php echo htmlspecialchars($user['fullname']); ?></h3>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <p>Phone: <?php echo htmlspecialchars($user['phone']); ?></p>
    </div>

    <h3>Purchase History (PC Games)</h3>
    <?php if(count($purchases) > 0): ?>
        <table style="width: 100%; text-align: left; margin-bottom: 20px; border-collapse: collapse;">
            <tr style="background-color: var(--surface);">
                <th style="padding: 10px;">Game</th>
                <th style="padding: 10px;">Price</th>
                <th style="padding: 10px;">Date</th>
                <th style="padding: 10px;">Status</th>
            </tr>
            <?php foreach($purchases as $p): ?>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 10px;"><?php echo htmlspecialchars($p['title']); ?></td>
                <td style="padding: 10px;"><?php echo format_price($p['total_price']); ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($p['purchase_date']); ?></td>
                <td style="padding: 10px; color: var(--success);"><?php echo htmlspecialchars($p['order_status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No purchases yet.</p>
    <?php endif; ?>

    <h3>Rental History (PS Games)</h3>
    <?php if(count($rentals) > 0): ?>
        <table style="width: 100%; text-align: left; margin-bottom: 20px; border-collapse: collapse;">
            <tr style="background-color: var(--surface);">
                <th style="padding: 10px;">Game</th>
                <th style="padding: 10px;">Console</th>
                <th style="padding: 10px;">Days</th>
                <th style="padding: 10px;">End Date</th>
                <th style="padding: 10px;">Status</th>
            </tr>
            <?php foreach($rentals as $r): ?>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 10px;"><?php echo htmlspecialchars($r['title']); ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($r['console']); ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($r['rental_days']); ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($r['end_date']); ?></td>
                <td style="padding: 10px; color: var(--warning);"><?php echo htmlspecialchars($r['rental_status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No rentals yet.</p>
    <?php endif; ?>

    <h3>Games Submitted for Sale</h3>
    <?php if(count($submissions) > 0): ?>
        <table style="width: 100%; text-align: left; margin-bottom: 20px; border-collapse: collapse;">
            <tr style="background-color: var(--surface);">
                <th style="padding: 10px;">Title</th>
                <th style="padding: 10px;">Platform</th>
                <th style="padding: 10px;">Price</th>
                <th style="padding: 10px;">Status</th>
            </tr>
            <?php foreach($submissions as $s): ?>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 10px;"><?php echo htmlspecialchars($s['title']); ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($s['platform']); ?></td>
                <td style="padding: 10px;"><?php echo format_price($s['asking_price']); ?></td>
                <td style="padding: 10px; color: var(--accent);"><?php echo htmlspecialchars($s['status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No games submitted for sale.</p>
    <?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>
