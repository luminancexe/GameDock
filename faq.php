<?php
require_once "includes/connection.php";
include "includes/header.php";
?>

<div class="container mt-4 form-container" style="max-width: 50rem;">
    <h2 class="text-center" style="color: var(--accent); margin-bottom: 1.875rem;">Frequently Asked Questions</h2>

    <div class="faq-item" style="margin-bottom: 1.5625rem; padding-bottom: 0.9375rem; border-bottom: 0.0625rem solid rgba(255,255,255,0.1);">
        <h4 style="color: var(--success); margin-bottom: 0.625rem;">How do PlayStation rentals work?</h4>
        <p style="color: #D1D5DB;">When you rent a PS4 or PS5 game, you select a rental duration (e.g., 3 days or 7 days). You will receive access to the game library for that time period. Make sure to return it before the expiration date to avoid late fees.</p>
    </div>

    <div class="faq-item" style="margin-bottom: 1.5625rem; padding-bottom: 0.9375rem; border-bottom: 0.0625rem solid rgba(255,255,255,0.1);">
        <h4 style="color: var(--success); margin-bottom: 0.625rem;">What payment methods are supported?</h4>
        <p style="color: #D1D5DB;">For this academic project, we use a Demo Payment gateway. In a live environment, we would support Credit/Debit Cards, bKash, and Nagad.</p>
    </div>

    <div class="faq-item" style="margin-bottom: 1.5625rem; padding-bottom: 0.9375rem; border-bottom: 0.0625rem solid rgba(255,255,255,0.1);">
        <h4 style="color: var(--success); margin-bottom: 0.625rem;">Can I sell my old digital games?</h4>
        <p style="color: #D1D5DB;">Yes! Simply navigate to the "Sell Game" tab in your profile, fill out the details about your game key or license, and submit it for review. Once approved, it will be listed in the store.</p>
    </div>

    <div class="faq-item" style="margin-bottom: 1.5625rem; padding-bottom: 0.9375rem; border-bottom: 0.0625rem solid rgba(255,255,255,0.1);">
        <h4 style="color: var(--success); margin-bottom: 0.625rem;">How do I access my purchased PC games?</h4>
        <p style="color: #D1D5DB;">After purchasing a PC game, the product key or download link will be available immediately in your Profile dashboard under "My Games".</p>
    </div>

    <div class="faq-item" style="margin-bottom: 1.5625rem; padding-bottom: 0.9375rem; border-bottom: 0.0625rem solid rgba(255,255,255,0.1);">
        <h4 style="color: var(--success); margin-bottom: 0.625rem;">Can I cancel a rental after checkout?</h4>
        <p style="color: #D1D5DB;">Rental orders can be canceled before activation. Once the rental period has started and access is granted, cancellation is no longer available.</p>
    </div>

    <div class="faq-item" style="margin-bottom: 1.5625rem; padding-bottom: 0.9375rem; border-bottom: 0.0625rem solid rgba(255,255,255,0.1);">
        <h4 style="color: var(--success); margin-bottom: 0.625rem;">Do you offer refunds for purchased games?</h4>
        <p style="color: #D1D5DB;">Refunds are considered only for duplicate payments or failed key delivery. For approved cases, the refund is processed back to the original payment method.</p>
    </div>

    <div class="faq-item" style="margin-bottom: 1.5625rem; padding-bottom: 0.9375rem; border-bottom: 0.0625rem solid rgba(255,255,255,0.1);">
        <h4 style="color: var(--success); margin-bottom: 0.625rem;">How do I change the currency shown on the site?</h4>
        <p style="color: #D1D5DB;">You can switch your preferred currency from the header currency selector. Prices across store, cart, and checkout will update automatically based on your selected option.</p>
    </div>

    <div class="faq-item" style="margin-bottom: 1.5625rem; padding-bottom: 0.9375rem; border-bottom: 0.0625rem solid rgba(255,255,255,0.1);">
        <h4 style="color: var(--success); margin-bottom: 0.625rem;">Where can I see my order history?</h4>
        <p style="color: #D1D5DB;">Your complete purchase and rental history is available in your Profile page. Each order includes date, item details, and current status.</p>
    </div>

    <div class="faq-item" style="margin-bottom: 1.5625rem; padding-bottom: 0.9375rem; border-bottom: 0.0625rem solid rgba(255,255,255,0.1);">
        <h4 style="color: var(--success); margin-bottom: 0.625rem;">Is my account data secure?</h4>
        <p style="color: #D1D5DB;">We follow standard security practices such as password hashing and protected sessions. You should still use a strong password and avoid sharing account credentials.</p>
    </div>

    <div class="faq-item" style="margin-bottom: 1.5625rem;">
        <h4 style="color: var(--success); margin-bottom: 0.625rem;">Who do I contact for support?</h4>
        <p style="color: #D1D5DB;">If you have any further questions, please visit our <a href="contact.php" style="color: var(--accent); font-weight: bold;">Contact page</a> to send us a direct message.</p>
    </div>

</div>

<?php include "includes/footer.php"; ?>
