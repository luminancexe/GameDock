<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/connection.php";

// "Remember me" — re-establishes the session from a long-lived cookie when the
// PHP session itself is gone (e.g. the browser was closed and reopened days
// later). The cookie carries the user_id plus a random token; only a SHA-256
// hash of the token is stored server-side, so a leaked users row can't be used
// to forge a login the way a raw user_id cookie could.
if (!isset($_SESSION['user_id']) && !empty($_COOKIE['remember_token'])) {
    $parts = explode(':', $_COOKIE['remember_token'], 2);
    if (count($parts) === 2) {
        [$rememberedUserId, $rememberedToken] = $parts;
        $stmt = $pdo->prepare("SELECT user_id, remember_token_hash, remember_token_expires FROM users WHERE user_id = ?");
        $stmt->execute([$rememberedUserId]);
        $rememberedUser = $stmt->fetch(PDO::FETCH_ASSOC);

        $valid = $rememberedUser
            && $rememberedUser['remember_token_hash']
            && strtotime($rememberedUser['remember_token_expires']) > time()
            && hash_equals($rememberedUser['remember_token_hash'], hash('sha256', $rememberedToken));

        if ($valid) {
            $_SESSION['user_id'] = (int)$rememberedUser['user_id'];
        } else {
            // Stale, expired, or tampered cookie — clear it so it isn't re-checked on every request.
            setcookie('remember_token', '', ['expires' => time() - 3600, 'path' => '/']);
        }
    }
}

function isLoggedIn() {
    return isset($_SESSION["user_id"]);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}
?>
