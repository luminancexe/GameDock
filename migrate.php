<?php
require_once "includes/connection.php";

$sql = "CREATE TABLE IF NOT EXISTS forum_posts (
  post_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
)";

try {
    $pdo->exec($sql);
    echo "forum_posts table created successfully.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
