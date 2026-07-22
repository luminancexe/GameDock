<?php
require_once "includes/currency.php";

$currency = $_GET['currency'] ?? 'USD';
if (array_key_exists($currency, CURRENCY_RATES)) {
    setcookie('currency', $currency, [
        'expires' => time() + 60 * 60 * 24 * 365, // 1 year
        'path' => '/',
        'samesite' => 'Lax',
    ]);
}

$redirect = $_SERVER['HTTP_REFERER'] ?? 'home.php';
header("Location: $redirect");
exit;
?>
