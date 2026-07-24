<?php
require_once "includes/connection.php";
include "includes/header.php";
?>

<div class="container mt-4 form-container faq-container">
    <h2 class="text-center faq-title">Frequently Asked Questions</h2>

    <div class="faq-item">
        <h4 class="faq-item-title">How do PlayStation and Xbox rentals work?</h4>
        <p class="faq-item-text">When you rent a PS 4/5 or Xbox Series X/S game, you select a rental duration (e.g., 3 days or 7 days) at checkout. Once your order is confirmed, you'll receive access to the game for that time period, shown under "My Rentals" in your Profile along with the exact expiration date and time. You can continue playing right up until the rental expires. Make sure to return it (or let it lapse naturally) before the expiration date to avoid late fees, and note that rental durations cannot be paused or extended mid-rental &mdash; if you need more time, you can place a new rental once the current one ends.</p>
    </div>

    <div class="faq-item">
        <h4 class="faq-item-title">What payment methods are supported?</h4>
        <p class="faq-item-text">For this academic project, we use a Demo Payment gateway that simulates a checkout flow without processing real transactions or storing real card data. In a live production environment, we would support Credit/Debit Cards, bKash, and Nagad, along with region-appropriate options depending on where the platform is deployed. All simulated transactions are for demonstration purposes only and do not reflect an actual payment processor.</p>
    </div>

    <div class="faq-item">
        <h4 class="faq-item-title">Can I sell my old digital games?</h4>
        <p class="faq-item-text">Yes! Simply navigate to the "Sell Game" tab in your profile, fill out the details about your game key or license (platform, title, condition/notes, and asking price), and submit it for review. Our team checks each listing for completeness and accuracy before it goes live. Once approved, it will be listed in the store and visible to other users; if a listing is rejected, you'll see the reason in your Profile and can edit and resubmit it. You can also edit the price or remove your own listing at any time before it sells.</p>
    </div>

    <div class="faq-item">
        <h4 class="faq-item-title">How do I access my purchased PC games?</h4>
        <p class="faq-item-text">After purchasing a PC game, the product key or download link will be available immediately in your Profile dashboard under "My Games". We recommend copying and safely storing your key right after purchase. If a key fails to activate on the relevant platform (e.g., Steam, Epic Games), reach out through the Contact page with your order number so we can investigate and, if it's a delivery issue on our end, issue a replacement key.</p>
    </div>

    <div class="faq-item">
        <h4 class="faq-item-title">Can I cancel a rental after checkout?</h4>
        <p class="faq-item-text">Rental orders can be canceled before activation, meaning any time between placing the order and the moment access is actually granted. Once the rental period has started and access is granted, cancellation is no longer available, since the rental duration begins counting down immediately upon activation. If you believe a rental was activated in error, contact support as soon as possible.</p>
    </div>

    <div class="faq-item">
        <h4 class="faq-item-title">Do you offer refunds for purchased games?</h4>
        <p class="faq-item-text">Refunds are considered only for duplicate payments or failed key delivery, since digital game keys and licenses generally cannot be "returned" once revealed. For approved cases, the refund is processed back to the original payment method and typically reflected within a few business days, depending on your payment provider's own processing times. To request a refund, contact us through the Contact page with your order number and a brief description of the issue.</p>
    </div>

    <div class="faq-item">
        <h4 class="faq-item-title">How do I change the currency shown on the site?</h4>
        <p class="faq-item-text">You can switch your preferred currency from the header currency selector. Prices across store, cart, and checkout will update automatically based on your selected option. Your choice is remembered for future visits via a cookie, so you won't need to reselect it every time. Note that displayed conversions are for demo purposes and are not tied to live market exchange rates.</p>
    </div>

    <div class="faq-item">
        <h4 class="faq-item-title">Where can I see my order history?</h4>
        <p class="faq-item-text">Your complete purchase and rental history is available in your Profile page. Each order includes the date, item details, price paid, current status (e.g., completed, active rental, expired, canceled), and, where applicable, the delivered key or download link. You can filter or search past orders if you have a large history.</p>
    </div>

    <div class="faq-item">
        <h4 class="faq-item-title">Is my account data secure?</h4>
        <p class="faq-item-text">We follow standard security practices such as password hashing, protected sessions, and access controls that limit who can view your account data. You should still use a strong, unique password, enable any available account protection features, and avoid sharing account credentials with anyone. If you ever suspect unauthorized access to your account, change your password immediately and contact support.</p>
    </div>

    <div class="faq-item">
        <h4 class="faq-item-title">What happens if I miss a rental's return window?</h4>
        <p class="faq-item-text">If a rental period expires without being renewed, access to that game is automatically revoked and the rental is marked as expired in your order history. Depending on the platform's policy, a late fee may apply if access continued past the agreed window. To avoid this, keep an eye on the expiration countdown shown under "My Rentals" and return or let the rental lapse before that date.</p>
    </div>

    <div class="faq-item">
        <h4 class="faq-item-title">Can I use GameDock on mobile devices?</h4>
        <p class="faq-item-text">Yes, the site is designed to be responsive and should work on most modern mobile browsers, letting you browse, purchase, and manage rentals from your phone or tablet. Some administrative or seller features may be easier to use on a larger screen, but core store and account functions are fully accessible on mobile.</p>
    </div>

    <div class="faq-item">
        <h4 class="faq-item-title">How do forum posts and community features work?</h4>
        <p class="faq-item-text">Registered users can create posts, comment, and react within the community forum. Please keep discussions respectful and on-topic; posts that violate our community guidelines may be removed, and repeated violations can result in restricted posting privileges. You can edit or delete your own posts and comments at any time from your Profile.</p>
    </div>

    <div class="faq-item" style="border-bottom: none;">
        <h4 class="faq-item-title">Who do I contact for support?</h4>
        <p class="faq-item-text">If you have any further questions, run into an issue with an order, or need help with your account, please visit our <a href="contact.php" class="policy-link">Contact page</a> to send us a direct message. Including your order number or account email when relevant helps us resolve your request faster.</p>
    </div>

</div>

<?php include "includes/footer.php"; ?>