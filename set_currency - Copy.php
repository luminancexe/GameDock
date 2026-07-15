<?php
require_once "includes/currency.php";

$currency = $_GET['currency'] ?? 'USD';
if (array_key_exists($currency, CURRENCY_RATES)) {
    $_SESSION['currency'] = $currency;
}

$redirect = $_SERVER['HTTP_REFERER'] ?? 'home.php';
header("Location: $redirect");
exit;
?>
