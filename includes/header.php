<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/currency.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GameDock is an academic project for buying, selling, and renting digital games.">
    <title>GameDock | Digital Gaming Platform</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="index.php" class="logo">
                <svg class="logo-mark" viewBox="0 0 104 104" aria-hidden="true">
                    <path d="M56 24 A28 28 0 1 0 56 80 L56 68" fill="none" stroke="#E8A33D" stroke-width="10" stroke-linecap="round"/>
                    <line x1="70" y1="30" x2="70" y2="74" stroke="#4FD1D9" stroke-width="10" stroke-linecap="round"/>
                    <path d="M70 30 A22 22 0 0 1 70 74" fill="none" stroke="#4FD1D9" stroke-width="10" stroke-linecap="round"/>
                </svg>
                Game<span>Dock</span>
            </a>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="pc_games.php">PC Games</a></li>
                <li class="dropdown">
                    <button class="dropdown-toggle" aria-haspopup="true" aria-expanded="false">Rentals</button>
                    <ul class="dropdown-menu">
                        <li><a href="ps_rentals.php">PS5</a></li>
                        <li><a href="xbox_rentals.php">Xbox</a></li>
                    </ul>
                </li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="wishlist.php">Wishlist</a></li>
                    <li><a href="sell_game.php">Sell Game</a></li>
                    <li><a href="profile.php">My Profile</a></li>
                    <li><a href="logout.php" style="color:var(--danger)">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login / Register</a></li>
                <?php endif; ?>
                <li><a href="contact.php">Contact</a></li>
                <li class="dropdown">
                    <?php $cur = current_currency(); ?>
                    <button class="dropdown-toggle" aria-haspopup="true" aria-expanded="false"><?php echo $cur; ?></button>
                    <ul class="dropdown-menu">
                        <li><a href="set_currency.php?currency=USD" class="<?php echo $cur === 'USD' ? 'active' : ''; ?>">USD</a></li>
                        <li><a href="set_currency.php?currency=BDT" class="<?php echo $cur === 'BDT' ? 'active' : ''; ?>">BDT</a></li>
                    </ul>
                </li>
            </ul>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>
