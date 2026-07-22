<?php
// AJAX endpoint: add, change, or remove a reaction on a forum post.
// Always responds with JSON — this is called via jQuery $.post from forum.php,
// never navigated to directly, so it never redirects like the page-based actions do.
require_once "includes/connection.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "You must be logged in to react."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
$reaction_type = $_POST['reaction_type'] ?? '';
$allowed_types = ['like', 'love', 'haha', 'wow', 'sad', 'angry', 'dislike'];

if (!$post_id || !in_array($reaction_type, $allowed_types, true)) {
    http_response_code(422);
    echo json_encode(["error" => "Invalid reaction request."]);
    exit;
}

$stmt = $pdo->prepare("SELECT reaction_id, reaction_type FROM forum_reactions WHERE user_id = ? AND post_id = ?");
$stmt->execute([$user_id, $post_id]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing && $existing['reaction_type'] === $reaction_type) {
    // Clicking the same reaction again removes it.
    $stmt = $pdo->prepare("DELETE FROM forum_reactions WHERE reaction_id = ?");
    $stmt->execute([$existing['reaction_id']]);
    $userReaction = null;
} elseif ($existing) {
    // Switching to a different reaction.
    $stmt = $pdo->prepare("UPDATE forum_reactions SET reaction_type = ? WHERE reaction_id = ?");
    $stmt->execute([$reaction_type, $existing['reaction_id']]);
    $userReaction = $reaction_type;
} else {
    $stmt = $pdo->prepare("INSERT INTO forum_reactions (post_id, user_id, reaction_type) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $reaction_type]);
    $userReaction = $reaction_type;
}

$stmt = $pdo->prepare("SELECT reaction_type, COUNT(*) AS c FROM forum_reactions WHERE post_id = ? GROUP BY reaction_type");
$stmt->execute([$post_id]);
$counts = array_fill_keys($allowed_types, 0);
$total = 0;
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $counts[$row['reaction_type']] = (int)$row['c'];
    $total += (int)$row['c'];
}

echo json_encode([
    "success" => true,
    "counts" => $counts,
    "total" => $total,
    "userReaction" => $userReaction,
]);
