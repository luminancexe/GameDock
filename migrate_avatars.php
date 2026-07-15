<?php
require_once "includes/connection.php";

try {
    $sql = "ALTER TABLE users ADD COLUMN profile_pic VARCHAR(255) DEFAULT 'default.png'";
    $pdo->exec($sql);
    echo "Added profile_pic column to users table successfully.\n";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
