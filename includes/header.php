<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GameHub is an academic project for buying, selling, and renting digital games.">
    <title>GameHub | Digital Gaming Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">GameHub</a>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="pc_games.php">PC Games</a></li>
                <li><a href="ps_rentals.php">PS Rentals</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="wishlist.php">Wishlist</a></li>
                    <li><a href="sell_game.php">Sell Game</a></li>
                    <li><a href="profile.php">My Profile</a></li>
                    <li><a href="logout.php" style="color:var(--danger)">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login / Register</a></li>
                <?php endif; ?>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
    </nav>
