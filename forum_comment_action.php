<?php
// AJAX endpoint: add or delete a comment on a forum post.
// Always responds with JSON — called via jQuery $.post from forum.php.
require_once "includes/connection.php";
require_once "includes/avatar_gallery_data.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "You must be logged in to comment."]);
    exit;
}

$user_id = $_SESSION['user_id'];

// The session can outlive the account it points to (e.g. deleted between
// login and this request); catch that here instead of letting the INSERT's
// FK constraint surface as an uncaught PDOException below.
$stmt = $pdo->prepare("SELECT 1 FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
if (!$stmt->fetch()) {
    session_unset();
    session_destroy();
    http_response_code(401);
    echo json_encode(["error" => "Your session has expired. Please log in again."]);
    exit;
}

$action = $_POST['action'] ?? 'add';

if ($action === 'delete') {
    $comment_id = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
    if (!$comment_id) {
        http_response_code(422);
        echo json_encode(["error" => "Invalid comment."]);
        exit;
    }
    $stmt = $pdo->prepare("DELETE FROM forum_comments WHERE comment_id = ? AND user_id = ?");
    $stmt->execute([$comment_id, $user_id]);
    if ($stmt->rowCount() === 0) {
        http_response_code(403);
        echo json_encode(["error" => "You can only delete your own comments."]);
        exit;
    }
    echo json_encode(["success" => true, "deleted" => $comment_id]);
    exit;
}

if ($action === 'edit') {
    $comment_id = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
    $content = trim($_POST['content'] ?? '');

    if (!$comment_id || $content === '') {
        http_response_code(422);
        echo json_encode(["error" => "Comment cannot be empty."]);
        exit;
    }
    if (mb_strlen($content) > 1000) {
        http_response_code(422);
        echo json_encode(["error" => "Comment is too long (1000 characters max)."]);
        exit;
    }

    // Checked as a separate SELECT rather than relying on UPDATE's row count —
    // MySQL reports 0 affected rows when the new value matches the old one,
    // which would otherwise misreport a genuine no-op edit as "not yours".
    $stmt = $pdo->prepare("SELECT user_id FROM forum_comments WHERE comment_id = ?");
    $stmt->execute([$comment_id]);
    $ownerId = $stmt->fetchColumn();
    if ($ownerId === false) {
        http_response_code(404);
        echo json_encode(["error" => "Comment not found."]);
        exit;
    }
    if ((int)$ownerId !== (int)$user_id) {
        http_response_code(403);
        echo json_encode(["error" => "You can only edit your own comments."]);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE forum_comments SET content = ? WHERE comment_id = ?");
    $stmt->execute([$content, $comment_id]);

    echo json_encode([
        "success" => true,
        "comment_id" => $comment_id,
        "content" => htmlspecialchars($content),
    ]);
    exit;
}

$post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
$content = trim($_POST['content'] ?? '');

if (!$post_id || $content === '') {
    http_response_code(422);
    echo json_encode(["error" => "Comment cannot be empty."]);
    exit;
}
if (mb_strlen($content) > 1000) {
    http_response_code(422);
    echo json_encode(["error" => "Comment is too long (1000 characters max)."]);
    exit;
}

$stmt = $pdo->prepare("SELECT post_id FROM forum_posts WHERE post_id = ?");
$stmt->execute([$post_id]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(["error" => "Post not found."]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO forum_comments (post_id, user_id, content) VALUES (?, ?, ?)");
$stmt->execute([$post_id, $user_id, $content]);
$comment_id = (int)$pdo->lastInsertId();

$stmt = $pdo->prepare("SELECT c.comment_id, c.content, c.created_at, u.fullname, u.profile_pic FROM forum_comments c JOIN users u ON c.user_id = u.user_id WHERE c.comment_id = ?");
$stmt->execute([$comment_id]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

$pic = $comment['profile_pic'] ?? 'default.png';
if ($pic === 'default.png') {
    $pic = default_avatar_for_seed($comment_id);
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM forum_comments WHERE post_id = ?");
$stmt->execute([$post_id]);
$total = (int)$stmt->fetchColumn();

echo json_encode([
    "success" => true,
    "comment" => [
        "comment_id" => $comment_id,
        "content" => htmlspecialchars($comment['content']),
        "fullname" => htmlspecialchars($comment['fullname']),
        "avatar" => htmlspecialchars($pic),
        "created_at" => date("F j, Y, g:i a", strtotime($comment['created_at'])),
        "mine" => true,
    ],
    "total" => $total,
]);
