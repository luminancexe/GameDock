<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// All prices are stored in the database as USD. Everything below is a fixed
// demo rate for display only — this project simulates payments, it doesn't
// fetch live exchange rates.
const CURRENCY_RATES = [
    'USD' => 1,
    'GBP' => 0.79,
    'EUR' => 0.92,
    'JPY' => 149,
    'CNY' => 7.2,
    'INR' => 83,
    'BDT' => 118,
    'CAD' => 1.36,
    'AUD' => 1.52,
    'CHF' => 0.88,
    'KRW' => 1360,
    'SEK' => 10.4,
];
const CURRENCY_SYMBOLS = [
    'USD' => '$',
    'GBP' => '£',
    'EUR' => '€',
    'JPY' => '¥',
    'CNY' => 'CN¥',
    'INR' => '₹',
    'BDT' => '৳',
    'CAD' => 'CA$',
    'AUD' => 'AU$',
    'CHF' => 'CHF',
    'KRW' => '₩',
    'SEK' => 'kr',
];

function current_currency() {
    $selected = $_COOKIE['currency'] ?? 'USD';
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