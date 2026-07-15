<?php
require_once "includes/connection.php";
include "includes/header.php";
?>

<div class="container mt-4 form-container" style="max-width: 800px;">
    <h2 class="text-center" style="color: var(--accent); margin-bottom: 30px;">Frequently Asked Questions</h2>

    <div class="faq-item" style="margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <h4 style="color: var(--success); margin-bottom: 10px;">How do PlayStation rentals work?</h4>
        <p style="color: #D1D5DB;">When you rent a PS4 or PS5 game, you select a rental duration (e.g., 3 days or 7 days). You will receive access to the game library for that time period. Make sure to return it before the expiration date to avoid late fees.</p>
    </div>

    <div class="faq-item" style="margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <h4 style="color: var(--success); margin-bottom: 10px;">What payment methods are supported?</h4>
        <p style="color: #D1D5DB;">For this academic project, we use a Demo Payment gateway. In a live environment, we would support Credit/Debit Cards, bKash, and Nagad.</p>
    </div>

    <div class="faq-item" style="margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <h4 style="color: var(--success); margin-bottom: 10px;">Can I sell my old digital games?</h4>
        <p style="color: #D1D5DB;">Yes! Simply navigate to the "Sell Game" tab in your profile, fill out the details about your game key or license, and submit it for review. Once approved, it will be listed in the store.</p>
    </div>

    <div class="faq-item" style="margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <h4 style="color: var(--success); margin-bottom: 10px;">How do I access my purchased PC games?</h4>
        <p style="color: #D1D5DB;">After purchasing a PC game, the product key or download link will be available immediately in your Profile dashboard under "My Games".</p>
    </div>

    <div class="faq-item" style="margin-bottom: 25px;">
        <h4 style="color: var(--success); margin-bottom: 10px;">Who do I contact for support?</h4>
        <p style="color: #D1D5DB;">If you have any further questions, please visit our <a href="contact.php" style="color: var(--accent); font-weight: bold;">Contact page</a> to send us a direct message.</p>
    </div>

</div>

<?php include "includes/footer.php"; ?>
