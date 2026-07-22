<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/connection.php";
require_once __DIR__ . "/auth_check.php"; // also re-establishes the session from a "remember me" cookie, if present
require_once __DIR__ . "/currency.php";

$current_page = basename($_SERVER['PHP_SELF']);
$nav_active = [
    'index.php' => 'home',
    'home.php' => 'home',
    'pc_games.php' => 'pc_games',
    'ps_rentals.php' => 'rentals',
    'xbox_rentals.php' => 'rentals',
    'forum.php' => 'community',
    'cart.php' => 'cart',
    'sell_game.php' => 'sell_game',
    'profile.php' => 'profile',
    'login.php' => 'login',
    'register.php' => 'login',
];
$active_nav = $nav_active[$current_page] ?? '';

// Cart item count for the nav badge — cart_items is DB-backed and tied to the
// account, so it's only meaningful once logged in.
$cartCount = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart_items WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cartCount = (int)$stmt->fetchColumn();
}

// Light/dark theme preference, read from a real cookie (not localStorage) so it
// can be rendered straight into the initial HTML — no flash-of-wrong-theme on
// first paint, and no anti-flash inline script needed.
$themeAttr = (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light') ? ' data-theme="light"' : '';

// Whether to show the cookie notice — only for visitors who haven't dismissed
// it yet. Checked server-side so it never flashes on screen for returning visitors.
$showCookieBanner = !isset($_COOKIE['cookie_consent']);

// Avatar shown in the nav's account menu — falls back to the bundled default icon.
$nav_profile_pic = 'default.png';
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $navUser = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!empty($navUser['profile_pic'])) {
        $nav_profile_pic = $navUser['profile_pic'];
    }
}
?>
<!DOCTYPE html>
<html lang="en"<?php echo $themeAttr; ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GameDock is an academic project for buying, selling, and renting digital games.">
    <title>GameDock | Digital Gaming Platform</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="stylesheet" href="css/style.css?v=<?php echo filemtime(__DIR__ . '/../css/style.css'); ?>">
</head>
<body>
    <?php if ($showCookieBanner): ?>
    <div class="cookie-banner" id="cookieBanner" role="dialog" aria-label="Cookie notice">
        <p>We use a few cookies to remember your theme, currency, and (if you choose) keep you signed in. See our <a href="privacy.php">Privacy Policy</a> for details.</p>
        <div class="cookie-banner-actions">
            <button type="button" id="cookieDecline" class="btn">No thanks</button>
            <button type="button" id="cookieAccept" class="btn btn-primary">Accept</button>
        </div>
    </div>
    <?php endif; ?>
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
                <li><a href="index.php" class="<?php echo $active_nav === 'home' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="pc_games.php" class="<?php echo $active_nav === 'pc_games' ? 'active' : ''; ?>">PC Games</a></li>
                <li class="dropdown <?php echo $active_nav === 'rentals' ? 'active' : ''; ?>">
                    <button class="dropdown-toggle <?php echo $active_nav === 'rentals' ? 'active' : ''; ?>" aria-haspopup="true" aria-expanded="false">Rentals</button>
                    <ul class="dropdown-menu">
                        <li><a href="ps_rentals.php" class="<?php echo $current_page === 'ps_rentals.php' ? 'active' : ''; ?>">PS5</a></li>
                        <li><a href="xbox_rentals.php" class="<?php echo $current_page === 'xbox_rentals.php' ? 'active' : ''; ?>">Xbox</a></li>
                    </ul>
                </li>
                <li><a href="forum.php" class="<?php echo $active_nav === 'community' ? 'active' : ''; ?>">Community</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="sell_game.php" class="<?php echo $active_nav === 'sell_game' ? 'active' : ''; ?>">Sell Game</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="<?php echo $active_nav === 'login' ? 'active' : ''; ?>">Login / Register</a></li>
                <?php endif; ?>
                <li class="dropdown">
                    <?php $cur = current_currency(); ?>
                    <button class="dropdown-toggle" aria-haspopup="true" aria-expanded="false"><?php echo $cur; ?></button>
                    <ul class="dropdown-menu">
                        <?php foreach (array_keys(CURRENCY_RATES) as $code): ?>
                            <li><a href="set_currency.php?currency=<?php echo $code; ?>" class="<?php echo $cur === $code ? 'active' : ''; ?>"><?php echo $code; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li>
                    <button id="themeToggle" class="theme-toggle" type="button" aria-label="Switch to light theme" aria-pressed="false">
                        <span class="theme-toggle-thumb">
                            <svg class="icon-sun" viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="4"/>
                                <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>
                            </svg>
                            <svg class="icon-moon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                            </svg>
                        </span>
                    </button>
                </li>
                <?php if(isset($_SESSION['user_id'])): ?>
                <li class="dropdown">
                    <button class="dropdown-toggle avatar-nav-toggle <?php echo $active_nav === 'profile' ? 'active' : ''; ?>" aria-haspopup="true" aria-expanded="false" aria-label="Account menu">
                        <img src="uploads/avatars/<?php echo htmlspecialchars($nav_profile_pic); ?>" alt="" class="avatar-nav-img">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="profile.php" class="<?php echo $active_nav === 'profile' ? 'active' : ''; ?>">My Profile</a></li>
                        <li>
                            <a href="cart.php" class="nav-cart-link <?php echo $active_nav === 'cart' ? 'active' : ''; ?>">
                                My Cart
                                <?php if ($cartCount > 0): ?><span class="nav-cart-badge"><?php echo $cartCount; ?></span><?php endif; ?>
                            </a>
                        </li>
                        <li><a href="logout.php" style="color:var(--danger)">Logout</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>
