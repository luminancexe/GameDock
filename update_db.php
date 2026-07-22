<?php
require_once "includes/connection.php";

try {
    // 1. Create Wishlist Table
    $sql1 = "CREATE TABLE IF NOT EXISTS wishlists (
        wishlist_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        game_id INT UNSIGNED NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_wishlist_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        CONSTRAINT fk_wishlist_game FOREIGN KEY (game_id) REFERENCES games(game_id) ON DELETE CASCADE,
        UNIQUE KEY user_game (user_id, game_id)
    ) ENGINE=InnoDB;";
    $pdo->exec($sql1);
    echo "Wishlists table created successfully.\n";

    // 2. Insert Games
    $sql2 = "INSERT INTO games (title, platform, category, service_type, purchase_price, rent_price, description, image, stock, status) VALUES 
    ('Elden Ring', 'PC', 'RPG', 'pc_purchase', 59.99, NULL, 'A massive open-world fantasy action-RPG.', 'elden-ring.jpg', 15, 'available'),
    ('Hogwarts Legacy', 'PC', 'RPG', 'pc_purchase', 49.99, NULL, 'An immersive, open-world action RPG set in the world of Harry Potter.', 'hogwarts-legacy.jpg', 20, 'available'),
    ('Spider-Man 2', 'PS5', 'Action', 'ps_rental', NULL, 15.00, 'Swing through New York as Peter Parker and Miles Morales.', 'spider-man-2.jpg', 5, 'available'),
    ('Ghost of Tsushima', 'PS5', 'Adventure', 'ps_rental', NULL, 10.00, 'Uncover the hidden wonders of Tsushima in this open-world action adventure.', 'ghost-of-tsushima.jpg', 3, 'available')";
    $pdo->exec($sql2);
    echo "Games inserted successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
