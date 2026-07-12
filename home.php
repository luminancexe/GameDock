<?php
require_once "includes/connection.php";
include "includes/header.php";
?>

<div class="container mt-4 text-center">
    <h1>Welcome to GameHub</h1>
    <p>Your one-stop destination for digital gaming.</p>
    <a href="pc_games.php" class="btn btn-primary mt-4">Browse PC Games</a>
    <a href="ps_rentals.php" class="btn btn-primary mt-4" style="background-color: var(--warning); color: #000;">Rent PS Games</a>
</div>

<div class="container mt-4">
    <h2>Featured Games</h2>
    <div class="game-grid">
        <!-- Sample UI for Featured Game -->
        <div class="game-card">
            <div style="background-color: #374151; height: 200px; display:flex; align-items:center; justify-content:center; color: #9CA3AF">No Image Available</div>
            <div class="game-card-content">
                <h3>Sample PC Game</h3>
                <p class="price">$59.99</p>
                <a href="game_details.php?id=1" class="btn btn-primary">View Details</a>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
