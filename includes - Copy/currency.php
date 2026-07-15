<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// All prices are stored in the database as USD. Everything below is a fixed
// demo rate for display only — this project simulates payments, it doesn't
// fetch live exchange rates.
const CURRENCY_RATES = [
    'USD' => 1,
    'BDT' => 118,
];
const CURRENCY_SYMBOLS = [
    'USD' => '$',
    'BDT' => '৳',
];

function current_currency() {
    $selected = $_SESSION['currency'] ?? 'USD';
    return array_key_exists($selected, CURRENCY_RATES) ? $selected : 'USD';
}

function currency_symbol() {
    return CURRENCY_SYMBOLS[current_currency()];
}

function convert_price($usd_amount) {
    return $usd_amount * CURRENCY_RATES[current_currency()];
}

function format_price($usd_amount, $suffix = '') {
    return currency_symbol() . number_format(convert_price($usd_amount), 2) . $suffix;
}
