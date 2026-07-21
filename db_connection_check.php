<?php
// Standalone connection test per assignment Task 1 (steps 5, 6, 11):
// visit this file directly in the browser to verify the MySQL connection.
require_once "includes/connection.php";

try {
    $stmt = $pdo->query("SELECT DATABASE() AS db, VERSION() AS version");
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    $gameCount = $pdo->query("SELECT COUNT(*) FROM games")->fetchColumn();
    echo "<h2>Database connection: OK</h2>";
    echo "<p>Connected to database <b>" . htmlspecialchars($info['db']) . "</b> (MySQL/MariaDB " . htmlspecialchars($info['version']) . ")</p>";
    echo "<p>Games table reachable, row count: " . (int)$gameCount . "</p>";
} catch (PDOException $e) {
    http_response_code(500);
    echo "<h2>Database connection: FAILED</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
